<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/24/2022
 * File: Genre.php
 * Description: This file defines the Genre model class
 */

namespace MusicAPI\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
//The table associated with this model
    protected $table = 'genres';

//The primary key of the table
    protected $primaryKey = 'id';

//The PK is non-numeric
    public $incrementing = false;

//If the PF is not an integer, set its type
    protected $keyType = 'char';

//If the created_at and updated_at columns are not used
    public $timestamps = false;

    //Get all tracks of a genre
    public function tracks()
    {
        return $this->belongsTo(Track::class, 'id', 'genre');
    }

    //Get all collections a genre belongs to
    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'tracks', 'genre', 'chapter')->withPivot('title', 'duration_ms');
    }

    //Retrieve all genres
    public static function getGenres()
    {
        //Retrieve all genres
        $genres = self::with('tracks')->get();
        return $genres;
    }

    //View a specific genres
    public static function getGenresById(string $id)
    {
        $genre = self::findOrFail($id);
        $genre->load('tracks');
        return $genre;
    }

    //Get a genre's collections
    public static function getGenreCollections(string $id)
    {
        return self::findOrFail($id)->collections;
    }

    //Get all tracks of a genre
    public static function getTracksByGenre(string $id) {
        $genres = self::findOrFail($id)->tracks;
        return $genres;
    }


    //Search for genres
    public static function searchGenres($term)
    {
        if (is_numeric($term)) {
            $query = self::where('year_origin', '>=', $term);
        } else {
            $query = self::where('genre', 'like', "%$term%")
                ->orWhere('geographic_origin', 'like', "%$term%");
        }
        return $query->get();
    }

    //Insert a new genre
    public static function createGenre($request)
    {
        //Retrieve parameters from request body
        $params = $request->getParsedBody();
        //Create a new Genre instance
        $genre = new Genre();
        //Set the genre's attributes
        foreach ($params as $field => $value) {
            $genre->$field = $value;
        }
        //Insert the genre into the database
        $genre->save();
        return $genre;
    }

    //Update a genre
    public static function updateGenre($request) {
        //Retrieve parameters from request body
        $params = $request->getParsedBody();
        //Retrieve id from the request url
        $id = $request->getAttribute('id');
        $genre = self::findOrFail($id);
        if(!$genre) {
            return false;
        }
        //update attributes of the genre
        foreach($params as $field => $value) {
            $genre->$field = $value;
        }
        //save the genre into the database
        $genre->save();
        return $genre;
    }

    //Delete an genre
    public static function deleteGenre($request) {
        //Retrieve id from the request
        $id = $request->getAttribute('id');
        $genre = self::findOrFail($id);
        return($genre ? $genre->delete() : $genre);
    }
}