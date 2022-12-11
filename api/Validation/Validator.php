<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/26/2022
 * File: Validator.php
 * Description: Defines the Validator class for validation purposes
 */

namespace MusicAPI\Validation;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;


class Validator
{
    private static array $errors = [];

    // A generic validation method. it returns true on success or false on failed validation.
    public static function validate($request, array $rules): bool
    {
        foreach ($rules as $field => $rule) {
            //Retrieve parameters from URL or the request body
            $param = $request->getAttribute($field) ?? $request->getParsedBody()[$field];
            try {
                $rule->setName($field)->assert($param);
            } catch (NestedValidationException $ex) {
                self::$errors[$field] = $ex->getFullMessage();
            }
        }
        // Return true or false; "false" means a failed validation.
        return empty(self::$errors);
    }

    //Validate artist data.
    public static function validateArtist($request): bool
    {
        //Define all the validation rules
        $rules = [
            'id' => v::notEmpty()->alnum()->startsWith('c')->length(6, 6),
            'name' => v::alnum(' '),
            'description' => v::alnum(' ', '.', ','),
            'image' => v::alnum('.', '-'),

        ];

        return self::validate($request, $rules);
    }

    public static function validateAlbum($request): bool
    {
        //Define all the validation rules
        $rules = [
            'number' => v::notEmpty()->alnum('-')->length(8, 10),
            'title' => v::alnum(' '),
            'description' => v::alnum(' ', '.', ','),
            'image' => v::alnum(' ', '.', ',', '/', ':'),
            'total_tracks' => v::numericVal(),

        ];

        return self::validate($request, $rules);
    }

    //Validate genre data.
    public static function validateGenre($request): bool
    {
        //Define all the validation rules
        $rules = [
            'id' => v::notEmpty()->alnum('-')->contains('-')->length(4, 4),
            'genre' => v::alnum(' '),
            'geographic_origin' => v::alpha(' '),
            'year_origin' => v::date('Y')
        ];

        return self::validate($request, $rules);
    }

    // Validate attributes of a User model. Do not validate fields having default values (id, created_at, and updated_at)
    public static function validateUser($request) : bool {
        $rules = [
            'first_name' => v::alnum(' '),
            'last_name' => v::alnum(' '),
            'email' => v::email(),
            'username' => v::notEmpty(),
            'password' => v::notEmpty(),
            'role' => v::number()->between(1, 4)
        ];

        return self::validate($request, $rules);
    }

    //Return the errors in an array
    public static function getErrors(): array
    {
        return self::$errors;
    }
}