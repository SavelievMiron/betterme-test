<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Review::class, 'review');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return ReviewResource::collection($user->reviews);
    }

    /**
     * Search resource
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $request->validate([
            'book'   => 'nullable|string',
            'author' => 'nullable|string',
            'rate'   => 'nullable|integer|between:0,5'
        ]);

        $query = Review::query();

        if ($request->filled('book')) {
            $query->whereHas('book', function (Builder $query) use ($request) {
                $query->where('title', $request->input('book'));
            });
        }

        if ($request->filled('author')) {
            $query->whereHas('author', function (Builder $query) use ($request) {
                $query->where('name', $request->input('author'));
            });
        }

        if ($request->filled('rate')) {
            $query->where('rate', $request->input('rate'));
        }

        $result = $query->get();

        return ReviewResource::collection($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreReviewRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReviewRequest $request)
    {
        $data = $request->only(['content', 'rate']);

        $review = Review::create($data);

        $review->author()->associate($request->user());

        $book = Book::where('title', $request->input('book'))->get()->first();
        if ( ! empty($boook)) {
            $review->book()->associate($book->id);
        }

        $review->save();

        return new ReviewResource($review);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Review $review
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Review $review)
    {
        return new ReviewResource($review);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateReviewRequest $request
     * @param \App\Models\Review $review
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        $review->update(
            $request->only(['content', 'rate'])
        );

        return new ReviewResource($review);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Review $review
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return response(null, 204);
    }
}
