<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/26/2022
 * File: TrackController.php
 * Description: Defines TracksController class
 */


namespace MusicAPI\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use MusicAPI\Models\Track;
use MusicAPI\Controllers\ControllerHelper as Helper;
use MusicAPI\Validation\Validator;


class TrackController
{
    //list all tracks
    public function index(Request $request, Response $response, array $args): Response
    {
        $params = $request->getQueryParams();
        $term = array_key_exists('q', $params) ? $params['q'] : "";

        //Call the model method to get tracks
        $results = ($term) ? Track::searchTracks($term) : Track::getTracks($request);


        return Helper::withJson($response, $results, 200);
    }

    //View a specific track
    public function view(Request $request, Response $response, array $args): Response
    {
        $results = Track::getTracksById($args['track_id']);
        return Helper::withJson($response, $results, 200);
    }

    //Get collections by track id
    public function viewCollections(Request $request, Response $response, array $args): Response
    {

        $results = Track::getTrackCollections($args['chapter']);
        return Helper::withJson($response, $results, 200);
    }

    //Get genres by track id
    public function viewGenres(Request $request, Response $response, array $args): Response
    {

        $results = Track::getGenresByTrack($args['genre']);
        return Helper::withJson($response, $results, 200);
    }

    //Get albums by track id
    public function viewAlbums(Request $request, Response $response, array $args): Response
    {

        $results = Track::getAlbumsByTrackId($args['track_id']);
        return Helper::withJson($response, $results, 200);
    }

    //Get albums by track id
    public function viewArtists(Request $request, Response $response, array $args): Response
    {

        $results = Track::getArtistsByTrackId($args['track_id']);
        return Helper::withJson($response, $results, 200);
    }


}