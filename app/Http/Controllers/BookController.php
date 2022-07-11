<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Book::class, 'book');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return BookResource::collection($user->books);
    }

    /**
     * Search resource
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $request->validate([
            'title'  => 'nullable|string',
            'author' => 'nullable|string',
        ]);

        $query = Book::query();

        if ($request->filled('title')) {
            $query->where('title', $request->input('title'));
        }

        if ($request->filled('author')) {
            $query->whereHas('author', function (Builder $query) use ($request) {
                $query->where('name', $request->input('author'));
            });
        }

        $result = $query->get();

        return BookResource::collection($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreBookRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBookRequest $request)
    {
        $book = Book::create([
            'title' => $request->input('title')
        ]);

        $book->author()->associate($request->user());
        $book->save();

        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Book $book
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        return new BookResource($book);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateBookRequest $request
     * @param \App\Models\Book $book
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update([
            'title' => $request->input('title')
        ]);

        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Book $book
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return response(null, 204);
    }
}
