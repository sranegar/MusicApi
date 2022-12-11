<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/24/2022
 * File: Artist.php
 * Description: Defines the Artist model class
 */

namespace MusicAPI\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{

//The table associated with this model
    protected $table = 'artists';

//The primary key of the table
    protected $primaryKey = 'id';

//The PK is non-numeric
    public $incrementing = false;

//If the PF is not an integer, set its type
    protected $keyType = 'char';

//If the created_at and updated_at columns are not used
    public $timestamps = false;

    public function collections()
    {
        return $this->hasMany(Collection::class, 'artist');
    }

    //Get all albums by artist
    public function albums()
    {
        return $this->belongsToMany(Album::class, 'collections', 'artist', 'album');
    }


    //Get all tracks by artist
    public function tracks()
    {
        return $this->hasManyThrough(Track::class, Collection::class, 'artist', 'chapter', 'id', 'chapter');
    }

    //Retrieve all artists
    public static function getArtists()
    {

        $artists = self::with(['albums', 'tracks'])->get();
        return $artists;
//        //get the total number of row count
//        $count = self::count();
//
//        //Get querystring variables from url
//        $params = $request->getQueryParams();
//
//        //do limit and offset exist?
//        $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 8;   //items per page
//        $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0;  //offset of the first item
//
//        //pagination
//        $links = self::getLinks($request, $limit, $offset);
//
//        //build query
//        $query = self::with(['albums', 'tracks']);  //build the query to get all albums
//        $query = $query->skip($offset)->take($limit);  //limit the rows
//
//        //code for sorting
//        $sort_key_array = self::getSortKeys($request);
//
//        //sort the output by one or more columns
//        foreach ($sort_key_array as $column => $direction) {
//            $query->orderBy($column, $direction);
//        }
//
//        //retrieve the artists
//        $artists = $query->get();  //Finally, run the query and get the results
//
//        //construct the data for response
//        $results = [
//            'totalCount' => $count,
//            'limit' => $limit,
//            'offset' => $offset,
//            'links' => $links,
//            'sort' => $sort_key_array,
//            'data' => $artists
//        ];
//
//        return $results;
    }

    //View a specific artist
    public static function getArtistsById(string $id)
    {
        $artist = self::findOrFail($id);
        $artist->load('albums');
        $artist->load('collections');
        $artist->load('tracks');
        return $artist;
    }

    //Get tracks by artist
    public static function getTracksByArtist(string $id)
    {
        $artist = self::findOrFail($id)->tracks;
        return $artist;
    }

    //view all artist's collections by an artist
    public static function getCollectionsByArtist(string $id)
    {
        $collections = self::findOrFail($id)->collections;
        return $collections;
    }

    //Get an artist's albums
    public static function getArtistsAlbums(string $id)
    {
        return self::findOrFail($id)->albums;
    }

    //Search for artists
    public static function searchArtists($term)
    {
        if ($term) {
            $query = self::where ('name', 'like', "%$term%");
        }

        return $query->get();
    }

    //Insert a new artist
    public static function createArtist($request)
    {
        //Retrieve parameters from request body
        $params = $request->getParsedBody();
        //Create a new Artist instance
        $artist = new Artist();
        //Set the artist's attributes
        foreach ($params as $field => $value) {
            $artist->$field = $value;
        }
        //Insert the artist into the database
        $artist->save();
        return $artist;
    }


    //Update a artist
    public static function updateArtist($request) {
        //Retrieve parameters from request body
        $params = $request->getParsedBody();
        //Retrieve id from the request url
        $id = $request->getAttribute('id');
        $artist = self::findOrFail($id);
        if(!$artist) {
            return false;
        }
        //update attributes of the artist
        foreach($params as $field => $value) {
            $artist->$field = $value;
        }
        //save the artist into the database
        $artist->save();
        return $artist;
    }

    //Delete an artist
    public static function deleteArtist($request) {
        //Retrieve id from the request
        $id = $request->getAttribute('id');
        $artist = self::findOrFail($id);
        return($artist ? $artist->delete() : $artist);
    }

    // Return an array of links for pagination. The array includes links for the current, first, next, and last pages.
//    private static function getLinks($request, $limit, $offset)
//    {
//        $count = self::count();
//
//        // Get request uri and parts
//        $uri = $request->getUri();
//        if ($port = $uri->getPort()) {
//            $port = ':' . $port;
//        }
//        $base_url = $uri->getScheme() . "://" . $uri->getHost() . $port . $uri->getPath();
//
//        // Construct links for pagination
//        $links = [];
//        $links[] = ['rel' => 'self', 'href' => "$base_url?limit=$limit&offset=$offset"];
//        $links[] = ['rel' => 'first', 'href' => "$base_url?limit=$limit&offset=0"];
//        if ($offset - $limit >= 0) {
//            $links[] = ['rel' => 'prev', 'href' => "$base_url?limit=$limit&offset=" . $offset - $limit];
//        }
//        if ($offset + $limit < $count) {
//            $links[] = ['rel' => 'next', 'href' => "$base_url?limit=$limit&offset=" . $offset + $limit];
//        }
//        $links[] = ['rel' => 'last', 'href' => "$base_url?limit=$limit&offset=" . $limit * (ceil($count / $limit) - 1)];
//
//        return $links;
//    }
//
//    private static function getSortKeys($request)
//    {
//        $sort_key_array = [];
//
//        // Get querystring variables from url
//        $params = $request->getQueryParams();
//
//        if (array_key_exists('sort', $params)) {
//            $sort = preg_replace('/^\[|\]$|\s+/', '', $params['sort']);  // remove white spaces, [, and ]
//            $sort_keys = explode(',', $sort); //get all the key:direction pairs
//            foreach ($sort_keys as $sort_key) {
//                $direction = 'asc';
//                $column = $sort_key;
//                if (strpos($sort_key, ':')) {
//                    list($column, $direction) = explode(':', $sort_key);
//                }
//                $sort_key_array[$column] = $direction;
//            }
//        }
//
//        return $sort_key_array;
//    }
}