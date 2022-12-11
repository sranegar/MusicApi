<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/24/2022
 * File: GenreController.php
 * Description: Defines the GenreController class
 */


namespace MusicAPI\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use MusicAPI\Models\Genre;
use MusicAPI\Controllers\ControllerHelper as Helper;
use MusicAPI\Validation\Validator;

class GenreController
{
    //list all genres
    public function index(Request $request, Response $response, array $args): Response
    {
        //$results = Genre::getGenres();

        //Get querystring variables from url
        $params = $request->getQueryParams();
        $term = array_key_exists('q', $params) ? $params['q'] : "";

        //Call the model method to get genres
        $results = ($term) ? Genre::searchGenres($term) : Genre::getGenres();

        return Helper::withJson($response, $results, 200);
    }

    //View a specific genre
    public function view(Request $request, Response $response, array $args): Response
    {
        $results = Genre::getGenresById($args['id']);
        return Helper::withJson($response, $results, 200);
    }

    //View genre's collections
    public function viewGenreCollections(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $results = Genre::getGenreCollections($id);
        return Helper::withJson($response, $results, 200);
    }

    //View all tracks
    public function viewTracks(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $results = Genre::getGenresById($id);
        return Helper::withJson($response, $results, 200);
    }

    //Create an genre
    public function create(Request $request, Response $response, array $args): Response
    {
        //Validate the request
        $validation = Validator::validateGenre($request);
        if (!$validation) {
            $results = [
                'status' => "Validation failed",
                'errors' => Validator::getErrors()
            ];
            return Helper::withJson($response, $results, 500);
        }

        //Create a new genre
        $genre = Genre::createGenre($request);
        if (!$genre) {
            $results['status'] = "Genre cannot been created.";
            return Helper::withJson($response, $results, 500);
        }
        $results = [
            'status' => "Genre has been created.",
            'data' => $genre
        ];
        return Helper::withJson($response, $results, 200);
    }

    //Update a genre
    public function update(Request $request, Response $response, array $args): Response
    {
        //Validate the request
        $validation = Validator::validateGenre($request);
        //if validation failed
        if (!$validation) {
            $results = [
                'status' => "Validation failed",
                'errors' => Validator::getErrors()
            ];
            return Helper::withJson($response, $results, 500);
        }
        $genre = Genre::updateGenre($request);
        if (!$genre) {
            $results['status'] = "Genre cannot been updated.";
            return Helper::withJson($response, $results, 500);
        }
        $results = [
            'status' => "Genre has been updated.",
            'data' => $genre
        ];
        return Helper::withJson($response, $results, 200);
    }

    //Delete an genre
    public function delete(Request $request, Response $response, array $args): Response
    {
        $genre = Genre::deleteGenre($request);

        if (!$genre) {
            $results['status'] = "Genre cannot been deleted.";
            return Helper::withJson($response, $results, 500);
        }

        $results['status'] = "Genre has been deleted.";
        return Helper::withJson($response, $results, 200);
    }
}