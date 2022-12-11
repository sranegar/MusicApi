<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/26/2022
 * File: Track.php
 * Description: Defines the Track model class
 */

namespace MusicAPI\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    //The table associated with this model
    protected $table = 'tracks';

    //The primary key of the table
    protected $primaryKey = 'track_id';

    //The PK is non-numeric
    public $incrementing = false;

    //If the PF is not an integer, set its type
    protected $keyType = 'char';

    //If the created_at and updated_at columns are not used
    public $timestamps = false;

    public function genres()
    {
        return $this->hasOne(Genre::class, 'id', 'genre');
    }

    public function albums()
    {
        return $this->hasOneThrough(Album::class, Collection::class, 'chapter', 'number', 'chapter', 'album');
    }


    public function artists()
    {
        return $this->hasOneThrough(Artist::class, Collection::class, 'chapter', 'id', 'chapter', 'artist');
    }

    public function collections()
    {
        return $this->belongsTo(Collection::class, 'chapter', 'chapter');
    }

    //Get a genre's collections
    public static function getTrackCollections(string $id)
    {
        return self::findOrFail($id)->collections;
    }

    //Get a track's genres
    public static function getGenresByTrack(string $id)
    {
        return self::findOrFail($id)->genres;
    }


    //Retrieve all tracks
    public static function getTracks($request)
    {
        //Retrieve all tracks
        //get the total number of row count
        $count = self::count();

        //Get querystring variables from url
        $params = $request->getQueryParams();

        //do limit and offset exist?
        $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 5;   //items per page
        $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0;  //offset of the first item

        //pagination
        $links = self::getLinks($request, $limit, $offset);

        //build query
        $query = self::with(['collections', 'genres', 'albums', 'artists']);  //build the query to get all albums
        $query = $query->skip($offset)->take($limit);  //limit the rows

        //code for sorting
        $sort_key_array = self::getSortKeys($request);

        //sort the output by one or more columns
        foreach ($sort_key_array as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        //retrieve the artists
        $tracks = $query->get();  //Finally, run the query and get the results

        //construct the data for response
        $results = [
            'totalCount' => $count,
            'limit' => $limit,
            'offset' => $offset,
            'links' => $links,
            'sort' => $sort_key_array,
            'data' => $tracks
        ];

        return $results;
    }

    //View a specific tracks
    public static function getTracksById(string $id)
    {
        $tracks = self::findOrFail($id);
        $tracks->load('collections');
        $tracks->load('albums');
        $tracks->load('genres');
        return $tracks;
    }

    //View album that a track is on
    public static function getAlbumsByTrackId(string $id)
    {
        return self::findOrFail($id)->albums;
    }

    //View album that a track is on
    public static function getArtistsByTrackId(string $id)
    {
        return self::findOrFail($id)->artists;
    }

    //View genres by track id
    public static function getGenreByTrack(string $id)
    {
        return self::findOrFail($id)->genres;
    }

    //Search for artists
    public static function searchTracks($term)
    {
        if ($term) {
            $query = self::where('title', 'like', "%$term%");
        }
        return $query->get();
    }

    // Return an array of links for pagination. The array includes links for the current, first, next, and last pages.
    private static function getLinks($request, $limit, $offset)
    {
        $count = self::count();

        // Get request uri and parts
        $uri = $request->getUri();
        if ($port = $uri->getPort()) {
            $port = ':' . $port;
        }
        $base_url = $uri->getScheme() . "://" . $uri->getHost() . $port . $uri->getPath();

        // Construct links for pagination
        $links = [];
        $links[] = ['rel' => 'self', 'href' => "$base_url?limit=$limit&offset=$offset"];
        $links[] = ['rel' => 'first', 'href' => "$base_url?limit=$limit&offset=0"];
        if ($offset - $limit >= 0) {
            $links[] = ['rel' => 'prev', 'href' => "$base_url?limit=$limit&offset=" . $offset - $limit];
        }
        if ($offset + $limit < $count) {
            $links[] = ['rel' => 'next', 'href' => "$base_url?limit=$limit&offset=" . $offset + $limit];
        }
        $links[] = ['rel' => 'last', 'href' => "$base_url?limit=$limit&offset=" . $limit * (ceil($count / $limit) - 1)];

        return $links;
    }

    private static function getSortKeys($request)
    {
        $sort_key_array = [];

        // Get querystring variables from url
        $params = $request->getQueryParams();

        if (array_key_exists('sort', $params)) {
            $sort = preg_replace('/^\[|\]$|\s+/', '', $params['sort']);  // remove white spaces, [, and ]
            $sort_keys = explode(',', $sort); //get all the key:direction pairs
            foreach ($sort_keys as $sort_key) {
                $direction = 'asc';
                $column = $sort_key;
                if (strpos($sort_key, ':')) {
                    list($column, $direction) = explode(':', $sort_key);
                }
                $sort_key_array[$column] = $direction;
            }
        }

        return $sort_key_array;
    }

}