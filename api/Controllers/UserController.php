<?php
/**
 * Author: Stephanie Ranegar
 * Date: 6/2/2022
 * File: UserController.php
 * Description:
 */
namespace MusicAPI\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use MusicAPI\Controllers\ControllerHelper as Helper;
use MusicAPI\Validation\Validator;
use MusicAPI\Models\User;
use MusicAPI\Models\Token;

class UserController {
    // List users
    public function index(Request $request, Response $response, array $args) : Response
    {
        $results = User::getUsers();
        return Helper::withJson($response, $results, 200);
    }

    // View a specific user by its id
    public function view(Request $request, Response $response, array $args) : Response
    {
        $results = User::getUserById($request->getAttribute('id'));
        return Helper::withJson($response, $results, 200);
    }

    // Create a user when the user signs up an account
    public function create(Request $request, Response $response, array $args) : Response
    {
        // Validate the request
        $validation = Validator::validateUser($request);

        // If validation failed
        if (!$validation) {
            $results = [
                'status' => "Validation failed",
                'errors' => Validator::getErrors()
            ];
            return Helper::withJson($response, $results, 500);
        }

        // Validation has passed; Proceed to create the user
        $user = User::createUser($request);

        if(!$user) {
            $results['status']= "User cannot been created.";
            return Helper::withJson($response, $results, 500);
        }

        $results = [
            'status' => 'User has been created',
            'data' => $user
        ];
        return Helper::withJson($response, $results, 201);
    }

    // Update a user
    public function update(Request $request, Response $response, array $args) : Response
    {
        // Validate the request
        $validation = Validator::validateUser($request);

        // If validation failed
        if (!$validation) {
            $results = [
                'status' => "Validation failed",
                'errors' => Validator::getErrors()
            ];
            return Helper::withJson($response, $results, 500);
        }

        //Validation has passed, proceed to update the user
        $user = User::updateUser($request);

        //If update has been failed
        if(!$user) {
            $results['status']= "User cannot been updated.";
            return Helper::withJson($response, $results, 500);
        }

        //Update was successful, send the confirmation
        $results = [
            'status' => "User has been updated.",
            'data' => $user
        ];

        return Helper::withJson($response, $results, 200);
    }

    // Delete a user
    public function delete(Request $request, Response $response, array $args) : Response
    {
        $user = User::deleteUser($request);

        if(!$user) {
            $results['status']= "User cannot been deleted.";
            return Helper::withJson($response, $results, 500);
        }

        $results['status'] = "User has been deleted.";
        return Helper::withJson($response, $results, 200);
    }

    // Validate a user’s username and password.
    // Return a Bearer token on success or error on failure.
    public function authBearer(Request $request, Response $response, array $args):Response    {
    //Retrieve username and password from the request body
        $params = $request->getParsedBody();
        $username = $params['username'];
        $password = $params['password'];

        //Verify username and password
        $user = User::authenticateUser($username, $password);
        if(!$user) {
            return Helper::withJson($response, ['Status' => 'Login failed.'], 401);
        }

        //Username and password are valid
        $token = Token::generateBearer($user->id);
        $results = ['Status' => 'Login successful', 'Token' => $token];

        return Helper::withJson($response, $results, 200);
    }

    //Validate a user's username and password. Return a JWT token on success or error on failure
    public function authJWT(Request $request, Response $response) : Response {
        //Retrieve username and password from the request body
        $params = $request->getParsedBody();
        $username = $params['username'];
        $password = $params['password'];

        //Verify username and password
        $user = User::authenticateUser($username, $password);

        if(!$user) {
            return Helper::withJson($response, ['Status' => 'Login failed.'], 401);
        }
        //Username and password are valid. Generate a JWT.
        $jwt = User::generateJWT($user->id);
        $results = [
            'Status' => 'Login successful',
            'jwt' => $jwt,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->role,
            'username' => $user->username,
        ];

    //return the results
        return Helper::withJson($response, $results, 200);
    }

    //Redirect a user to log into a Google account or return a token object
    public function oauth2(Request $request, Response $response) : Response {
        //Try to get a Google access code from a URL querystring variable named code
        $code = $request->getQueryParams()['code'] ?? '';

        //Generate an Oauth2 token with the code
        $token = User::generateOauth2($code);

        //Is it a redirect URL?
        if(filter_var($token, FILTER_VALIDATE_URL)) {
            return $response = $response->withHeader('Location', $token)->withStatus(301);
        }

        //Token is invalid
        if(!$token) {
            return Helper::withJson($response, ['Status' => 'Login failed.'], 401);
        }
        //Token is valid
        $results = [
            'Status' => 'Login successful',
            'Token' => $token
        ];

        return Helper::withJson($response, $results, 200);
    }

}

