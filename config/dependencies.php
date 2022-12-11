<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/24/2022
 * File: dependencies.php
 * Description: This file contains all dependencies
 */

use DI\Container;

use MusicAPI\Controllers\{
    ArtistController,
    AlbumController,
    GenreController,
    CollectionController,
    TrackController,
    UserController
};


return function (Container $container) {
    // Set a dependency called "Artist"
    $container->set('Artist', function () {
        return new ArtistController();
    });

    // Set a dependency called "Album"
    $container->set('Album', function () {
        return new AlbumController();
    });

    // Set a dependency called "Genre"
    $container->set('Genre', function () {
        return new GenreController();
    });

    // Set a dependency called "Collection"
    $container->set('Collection', function () {
        return new CollectionController();
    });

    // Set a dependency called "Track"
    $container->set('Track', function () {
        return new TrackController();
    });

    // Set a dependency called "User"
    $container->set('User', function() {
        return new UserController();
    });
};