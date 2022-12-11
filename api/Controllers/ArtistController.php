<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/24/2022
 * File: ArtistController.php
 * Description: Defines the ArtistController class
 */

namespace MusicAPI\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use MusicAPI\Models\Artist;
use MusicAPI\Controllers\ControllerHelper as Helper;
use MusicAPI\Validation\Validator;


class ArtistController
{
    //list all artists
    public function index(Request $request, Response $response, array $args): Response
    {
        //$results = Artist::getArtists();

        //Get querystring variables from url
        $params = $request->getQueryParams();
        $term = array_key_exists('q', $params) ? $params['q'] : "";


        //Call the model method to get artists
        $results = ($term) ? Artist::searchArtists($term) : Artist::getArtists();
        foreach ($results as $result) {
            $result["image"] = $this->getImageBaseUrl($request) . $result["image"];
        }
        return Helper::withJson($response, $results, 200);
    }


    public function viewCollections(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $results = Artist::getCollectionsByArtist($id);
        return Helper::withJson($response, $results, 200);
    }

    //View a specific artist
    public function view(Request $request, Response $response, array $args): Response
    {
        $results = Artist::getArtistsById($args['id']);
        $results['image'] = $this->getImageBaseUrl($request) . $results["image"];

        foreach ($results['albums'] as $result) {
            $result['image'] = $this->getImageBaseUrl($request) . $result["image"];
        }
        foreach ($results['tracks'] as $result) {
            $result['mp3file'] = $this->getMusicBaseUrl($request) . $result["mp3file"];
        }


        return Helper::withJson($response, $results, 200);
    }

    //View artist's albums
    public function viewArtistsAlbums(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $results = Artist::getArtistsAlbums($id);
        foreach ($results as $result) {
            $result['image'] = $this->getImageBaseUrl($request) . $result["image"];
        }
        return Helper::withJson($response, $results, 200);
    }


    //View all tracks by artist
    public function viewTracks(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $results = Artist::getTracksByArtist($id);
        return Helper::withJson($response, $results, 200);
    }

    //Create an artist
    public function create(Request $request, Response $response, array $args): Response
    {
        //Validate the request
        $validation = Validator::validateArtist($request);
        if (!$validation) {
            $results = [
                'status' => "Validation failed",
                'errors' => Validator::getErrors()
            ];
            return Helper::withJson($response, $results, 500);
        }

        //Create a new artist
        $artist = Artist::createArtist($request);
        if (!$artist) {
            $results['status'] = "Artist cannot been created.";
            return Helper::withJson($response, $results, 500);
        }
        $results = [
            'status' => "Artist has been created.",
            'data' => $artist
        ];
        return Helper::withJson($response, $results, 200);
    }

    //Update a artist
    public function update(Request $request, Response $response, array $args): Response
    {
        //Validate the request
        $validation = Validator::validateArtist($request);
        //if validation failed
        if (!$validation) {
            $results = [
                'status' => "Validation failed",
                'errors' => Validator::getErrors()
            ];
            return Helper::withJson($response, $results, 500);
        }
        $artist = Artist::updateArtist($request);
        if (!$artist) {
            $results['status'] = "Artist cannot been updated.";
            return Helper::withJson($response, $results, 500);
        }
        $results = [
            'status' => "Artist has been updated.",
            'data' => $artist
        ];
        return Helper::withJson($response, $results, 200);
    }

    //Delete an artist
    public function delete(Request $request, Response $response, array $args): Response
    {
        $artist = Artist::deleteArtist($request);

        if (!$artist) {
            $results['status'] = "Artist cannot been deleted.";
            return Helper::withJson($response, $results, 500);
        }

        $results['status'] = "Artist has been deleted.";
        return Helper::withJson($response, $results, 200);
    }

    // Get the path to image url. Images are stored inside public/images folder.
    private function getImageBaseUrl(Request $request): string
    {
        $uri = $request->getUri();
        $port = $uri->getPort() ? ":" . $uri->getPort() : "";
        $routeContext = \Slim\Routing\RouteContext::fromRequest($request);
        return $uri->getScheme() . "://" . $uri->getHost() . $port . $routeContext->getBasePath() . "/public/images/";
    }

    private function getMusicBaseUrl(Request $request): string
    {
        $uri = $request->getUri();
        $port = $uri->getPort() ? ":" . $uri->getPort() : "";
        $routeContext = \Slim\Routing\RouteContext::fromRequest($request);
        return $uri->getScheme() . "://" . $uri->getHost() . $port . $routeContext->getBasePath() . "/public/music/";
    }

}