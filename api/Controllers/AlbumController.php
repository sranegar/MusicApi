<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/24/2022
 * File: AlbumController.php
 * Description: Defines the AlbumController class
 */

namespace MusicAPI\Controllers;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use MusicAPI\Models\Album;
use MusicAPI\Controllers\ControllerHelper as Helper;
use MusicAPI\Validation\Validator;

class AlbumController
{
    //list all albums
    public function index(Request $request, Response $response, array $args): Response
    {
        //Get querystring variables from url
        $params = $request->getQueryParams();
        $term = array_key_exists('q', $params) ? $params['q'] : "";

        //Call the model method to get albums
        $results = ($term) ? Album::searchAlbums($term) : Album::getAlbums();
        foreach ($results as $result) {
            $result["image"] = $this->getImageBaseUrl($request) . $result["image"];
        }

        return Helper::withJson($response, $results, 200);
    }

    //View a specific album
    public function view(Request $request, Response $response, array $args): Response
    {
        $results = Album::getAlbumsById($args['number']);
        $results['image'] = $this->getImageBaseUrl($request) . $results["image"];
        foreach ($results['tracks'] as $result) {
            $result['mp3file'] = $this->getMusicBaseUrl($request) . $result["mp3file"];
        }
        return Helper::withJson($response, $results, 200);
    }

    //View album's by album
    public function viewArtists(Request $request, Response $response, array $args): Response
    {
        $id = $args['number'];
        $results = Album::getArtistsByAlbum($id);
        return Helper::withJson($response, $results, 200);
    }

    //View all album collections by album
    public function viewCollections(Request $request, Response $response, array $args): Response
    {
        $id = $args['number'];
        $results = Album::getCollectionsByAlbum($id);
        return Helper::withJson($response, $results, 200);
    }

    //View all tracks on album
    public function viewTracks(Request $request, Response $response, array $args): Response
    {
        $id = $args['number'];
        $results = Album::getTracksByAlbum($id);
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

    //Create an album
    public function create(Request $request, Response $response, array $args): Response
    {
        //Validation not set up yet
        //Validate the request
        $validation = Validator::validateAlbum($request);
        if (!$validation) {
            $results = [
                'status' => "Validation failed",
                'errors' => Validator::getErrors()
            ];
            return Helper::withJson($response, $results, 500);
        }

        //Create a new album
        $album = Album::createAlbum($request);
        if (!$album) {
            $results['status'] = "Album cannot been created.";
            return Helper::withJson($response, $results, 500);
        }
        $results = [
            'status' => "Album has been created.",
            'data' => $album
        ];
        return Helper::withJson($response, $results, 200);
    }

    //Update a album
    public function update(Request $request, Response $response, array $args): Response
    {
        //Validate the request
        $validation = Validator::validateAlbum($request);
        //if validation failed
        if (!$validation) {
            $results = [
                'status' => "Validation failed",
                'errors' => Validator::getErrors()
            ];
            return Helper::withJson($response, $results, 500);
        }
        $album = Album::updateAlbum($request);
        if (!$album) {
            $results['status'] = "Album cannot been updated.";
            return Helper::withJson($response, $results, 500);
        }
        $results = [
            'status' => "Album has been updated.",
            'data' => $album
        ];
        return Helper::withJson($response, $results, 200);
    }

    //Delete an album
    public function delete(Request $request, Response $response, array $args): Response
    {
        $album = Album::deleteAlbum($request);

        if (!$album) {
            $results['status'] = "Album cannot been deleted.";
            return Helper::withJson($response, $results, 500);
        }

        $results['status'] = "Album has been deleted.";
        return Helper::withJson($response, $results, 200);
    }



    private function getMusicBaseUrl(Request $request): string
    {
        $uri = $request->getUri();
        $port = $uri->getPort() ? ":" . $uri->getPort() : "";
        $routeContext = \Slim\Routing\RouteContext::fromRequest($request);
        return $uri->getScheme() . "://" . $uri->getHost() . $port . $routeContext->getBasePath() . "/public/music/";
    }
}