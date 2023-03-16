<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/24/2022
 * File: routes.php
 * Description: File for routes in the application
 */

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use MusicAPI\Authentication\{
    MyAuthenticator,
    BasicAuthenticator,
    BearerAuthenticator,
    JWTAuthenticator,
    OAuth2Authenticator
};

return function (App $app) {
    //Set up CORS (Cross-Origin Resource Sharing) https://www.slimframework.com/docs/v4/cookbook/enable-cors.html
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    $app->add(function ($request, $handler) {
        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    });

    // Define app route
    $app->get('/', function (Request $request, Response $response, array $args) {
        $response->getBody()->write('Welcome to MyMusic API!');
        return $response;
    });

    // Add another route
    $app->get('/api/hello/{name}', function (Request $request, Response $response, array $args) {
        $response->getBody()->write("Hello " . $args['name']);
        return $response;
    });

    // User route group
    $app->group('/api/v1/users', function (RouteCollectorProxy $group) {
        $group->get('', 'User:index');
        $group->get('/oauth2', 'User:oauth2');
        $group->get('/{id}', 'User:view');
        $group->post('', 'User:create');
        $group->put('/{id}', 'User:update');
        $group->delete('/{id}', 'User:delete');
        $group->post('/authBearer', 'User:authBearer');
        $group->post('/authJWT', 'User:authJWT');
    });

    //Route group for api/v1 pattern
    $app->group('/api/v1', function (RouteCollectorProxy $group) {
        //Route group for artists pattern
        $group->group('/artists', function (RouteCollectorProxy $group) {
            //Call the index method defined in the ArtistController class
            //Artist is the container key defined in dependencies.php.
            $group->get('', 'Artist:index');
            $group->get('/{id}', 'Artist:view');
            $group->get('/{id}/collections', 'Artist:viewCollections');
            $group->get('/{id}/albums', 'Artist:viewArtistsAlbums');
            $group->get('/{id}/tracks', 'Artist:viewTracks');
            $group->post('', 'Artist:create');
            $group->put('/{id}', 'Artist:update');
            $group->delete('/{id}', 'Artist:delete');
        });

        //Route group for albums pattern
        $group->group('/albums', function (RouteCollectorProxy $group) {
            //Call the index method defined in the AlbumController class
            //Album is the container key defined in dependencies.php.
            $group->get('', 'Album:index');
            $group->get('/{number}', 'Album:view');
            $group->get('/{number}/artists', 'Album:viewArtists');
            $group->get('/{number}/collections', 'Album:viewCollections');
            $group->get('/{number}/tracks', 'Album:viewTracks');
            $group->post('', 'Album:create');
            $group->put('/{number}', 'Album:update');
            $group->delete('/{number}', 'Album:delete');

        });

        //Route group for genres pattern
        $group->group('/genres', function (RouteCollectorProxy $group) {
            $group->get('', 'Genre:index');
            $group->get('/{id}', 'Genre:view');
            $group->get('/{id}/collections', 'Genre:viewGenreCollections');
            $group->get('/{id}/tracks', 'Genre:viewTracks');
            $group->post('', 'Genre:create');
            $group->put('/{id}', 'Genre:update');
            $group->delete('/{id}', 'Genre:delete');
        });


        //Route group for genres pattern
        $group->group('/collections', function (RouteCollectorProxy $group) {
            $group->get('', 'Collection:index');
            $group->get('/{chapter}', 'Collection:view');
            $group->get('/{chapter}/genres', 'Collection:viewGenres');
            $group->get('/{chapter}/tracks', 'Collection:viewTracks');
            $group->get('/{chapter}/albums', 'Collection:viewAlbums');
        });


        //Route group for tracks pattern
        $group->group('/tracks', function (RouteCollectorProxy $group) {
            $group->get('', 'Track:index');
            $group->get('/{track_id}', 'Track:view');
            $group->get('/{chapter}/collections', 'Track:viewCollections');
            $group->get('/{track_id}/albums', 'Track:viewAlbums');
            $group->get('/{track_id}/artists', 'Track:viewArtists');
            $group->get('/{genre}/genres', 'Track:viewGenres');

        });
        });
        //})->add(new MyAuthenticator());
        //})->add(new BasicAuthenticator());
        //})->add(new BearerAuthenticator());
//          })->add(new JWTAuthenticator());
        //})->add(new OAuth2Authenticator());

};