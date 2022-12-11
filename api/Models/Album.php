<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/24/2022
 * File: Album.php
 * Description: Defines the Album model class
 */

namespace MusicAPI\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
//The table associated with this model
    protected $table = 'albums';

//The primary key of the table
    protected $primaryKey = 'number';

//The PK is non-numeric
    public $incrementing = false;

//If the PF is not an integer, set its type
    protected $keyType = 'char';

//If the created_at and updated_at columns are not used
    public $timestamps = false;

    public function artists()
    {
        return $this->belongsToMany(Artist::class, 'collections', 'album', 'artist');
    }

    public function collections()
    {
        return $this->hasMany(Collection::class, 'album');
    }

    public function tracks()
    {
        return $this->hasManyThrough(Track::class, Collection::class, 'album', 'chapter', 'number', 'chapter');
    }


    //Retrieve all albums
    public static function getAlbums()
    {
        $albums = self::with(['artists', 'tracks'])->get();
        return $albums;
    }

    //View a specific student
    public static function getAlbumsById(string $number)
    {
        $album = self::findOrFail($number);
        $album->load('artists');
        $album->load('tracks');
        $album->load('collections');
        return $album;
    }

    //Get tracks on album
    public static function getTracksByAlbum(string $number)
    {
        $album = self::findOrFail($number)->tracks;
        return $album;
    }

    //Get an artist's albums
    public static function getArtistsByAlbum(string $number)
    {
        return self::findOrFail($number)->artists;
    }

    //view all album's collections by album
    public static function getCollectionsByAlbum(string $number)
    {
        $collections = self::findOrFail($number)->collections;
        return $collections;
    }

    //Search for albums
    public static function searchAlbums($term)
    {
        if ($term) {
            $query = self::with('artists', 'tracks')->where('title', 'like', "%$term%");
        }
        return $query->get();
    }

    //Insert a new artist
    public static function createAlbum($request)
    {
        //Retrieve parameters from request body
        $params = $request->getParsedBody();
        //Create a new Album instance
        $album = new Album();
        //Set the album's attributes
        foreach ($params as $field => $value) {
            $album->$field = $value;
        }
        //Insert the album into the database
        $album->save();
        return $album;
    }

    //Update a artist
    public static function updateAlbum($request) {
        //Retrieve parameters from request body
        $params = $request->getParsedBody();
        //Retrieve id from the request url
        $id = $request->getAttribute('number');
        $album = self::findOrFail($id);
        if(!$album) {
            return false;
        }
        //update attributes of the album
        foreach($params as $field => $value) {
            $album->$field = $value;
        }
        //save the album into the database
        $album->save();
        return $album;
    }

    //Delete an artist
    public static function deleteAlbum($request) {
        //Retrieve id from the request
        $id = $request->getAttribute('number');
        $album = self::findOrFail($id);
        return($album ? $album->delete() : $album);
    }


}