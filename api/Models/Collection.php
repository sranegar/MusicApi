<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/24/2022
 * File: Collection.php
 * Description: Defines the Collection model class
 */

namespace MusicAPI\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{

//The table associated with this model
    protected $table = 'collections';

//The primary key of the table
    protected $primaryKey = 'chapter';

//The PK is non-numeric
    public $incrementing = false;

//If the PF is not an integer, set its type
    protected $keyType = 'char';

//If the created_at and updated_at columns are not used
    public $timestamps = false;

    //Get all tracks of a collection
    public function tracks()
    {
        return $this->hasMany(Track::class, 'chapter');
    }

    //Get all albums of a collection
    public function albums()
    {
        return $this->hasMany(Album::class, 'number' , 'album');

    }

    //Get all genres a collection belongs to
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'tracks', 'chapter', 'genre')->withPivot('title', 'duration_ms');
    }

    //Retrieve all artists
    public static function getCollections()
    {
        //Retrieve all artists
        $collections = self::all();
        return $collections;
    }

    //View a specific student
    public static function getCollectionsById(int $chapter)
    {
        $collection = self::findOrFail($chapter);
        $collection->load('tracks');
        $collection->load('albums');
        $collection->load('artists');
        return $collection;
    }

    public static function getTracksByCollection(int $chapter) {
        $tracks = self::findOrFail($chapter)->tracks;
        return $tracks;
    }

    public static function getGenreByCollection(int $chapter)
    {
        return self::findOrFail($chapter)->genres;
    }

    public static function getAlbumByCollectionId(string $chapter)
    {
        return self::findOrFail($chapter)->albums;
    }
}