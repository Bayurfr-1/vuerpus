<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class BookController extends Controller
{
    public function find(Request $request, $limit = 10, $offset = 0)
    {
        $find = $request->find;
        $book = Book::where("id","like","%$find%")
        ->orWhere("title","like","%$find%")
        ->orWhere("description","like","%$find%");
        $data["count"] = $book->count();
        $books = array();
        foreach ($book->skip($offset)->take($limit)->get() as $p) {
          $item = [
            "id" => $p->id,
            "title" => $p->title,
            "description" => $p->description,
            "created_at" => $p->created_at,
            "updated_at" => $p->updated_at
          ];
          array_push($books,$item);
        }
        $data["book"] = $books;
        $data["status"] = 1;
        return response($data);
    }

    public function delete($id)
    {
        try{

            Book::where("id", $id)->delete();

            return response([
            	"status"	=> 1,
                "message"   => "Data berhasil dihapus."
            ]);
        } catch(\Exception $e){
            return response([
            	"status"	=> 0,
                "message"   => $e->getMessage()
            ]);
        }
    }

	public function getAll($limit = 10, $offset = 0){
        $data["count"] = Book::count();
        $book = array();

        foreach (Book::take($limit)->skip($offset)->get() as $p) {
            $item = [
                "id"          => $p->id,
                "title"        => $p->title,
                "description"   => $p->description,
                "created_at"  => $p->created_at,
                "updated_at"  => $p->updated_at
            ];

            array_push($book, $item);
        }
        $data["book"] = $book;
        $data["status"] = 1;
        return response($data);
    }

	public function store (Request $request)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'required|string|max:255',
			'description' => 'required|string|max:255',
		]);

		if($validator->fails()){
			return response()->json([
				'status'	=> 0,
				'message'	=> $validator->errors()->toJson()
			]);
		}

		$book = new Book();
		$book->title 	= $request->title;
		$book->description 	= $request->description;
		$book->save();

		return response()->json([
			'status'	=> '1',
			'message'	=> 'Book berhasil terregistrasi'
		 //'book'		=> $book,
		], 201);
	}

	public function update(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'required|string',
			'description' => 'required|string',
		]);

		if($validator->fails()){
			return response()->json([
				'status'	=> '0',
				'message'	=> $validator->errors()
			]);
		}

		//proses update data
		$book = Book::where('id', $request->id)->first();
		$book->title 	= $request->title;
		$book->description 	= $request->description;
		$book->save();


		return response()->json([
			'status'	=> '1',
			'message'	=> 'Book berhasil diubah'
		], 201);
	}

	public function getAuthenticatedBook(){
		try {
			if(!$book = JWTAuth::parseToken()->authenticate()){
				return response()->json([
						'auth' 		=> false,
						'message'	=> 'Invalid token'
					]);
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
			return response()->json([
						'auth' 		=> false,
						'message'	=> 'Token expired'
					], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
			return response()->json([
						'auth' 		=> false,
						'message'	=> 'Invalid token'
					], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e){
			return response()->json([
						'auth' 		=> false,
						'message'	=> 'Token absent'
					], $e->getStatusCode());
		}

		 return response()->json([
		 		"auth"      => true,
                "book"    => $book
		 ], 201);
	}

}

