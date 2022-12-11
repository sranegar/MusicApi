<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/24/2022
 * File: CollectionController.php
 * Description: Defines the CollectionController class
 */

namespace MusicAPI\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use MusicAPI\Models\Collection;
use MusicAPI\Controllers\ControllerHelper as Helper;

class CollectionController
{
    //list all artists
    public function index(Request $request, Response $response, array $args): Response
    {
        $results = Collection::getCollections();
        return Helper::withJson($response, $results, 200);
    }

    //View a specific artist
    public function view(Request $request, Response $response, array $args): Response
    {
        $results = Collection::getCollectionsById($args['chapter']);
        return Helper::withJson($response, $results, 200);
    }

    //View genres by collection
    public function viewGenres(Request $request, Response $response, array $args): Response
    {
        $results = Collection::getGenreByCollection($args['chapter']);
        return Helper::withJson($response, $results, 200);
    }

    //View all tracks of a collection
    public function viewTracks(Request $request, Response $response, array $args): Response
    {
        $id = $args['chapter'];
        $results = Collection::getTracksByCollection($id);
        return Helper::withJson($response, $results, 200);
    }

    //View album by collection id
    public function viewAlbums(Request $request, Response $response, array $args): Response
    {

        $results = Collection::getAlbumByCollectionId($args['chapter']);
        return Helper::withJson($response, $results, 200);
    }
}