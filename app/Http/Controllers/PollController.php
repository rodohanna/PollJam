<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Poll;
use App\Option;

class PollController extends Controller
{
    /**
     * Gets a poll.
     *
     * @param UUID $id
     * @return void
     */
    public function get($id)
    {
        $poll = Poll::find($id);
        if (!$poll) {
            return view('welcome');
        }
        return view('vote')->with('poll', $poll->toArray());
    }

    /**
     * Creates a new poll.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $custom_error_message = [
            'size' => 'The :attribute must have at least one element.',
            'options.min' => 'There must be at least one :attribute.',
        ];
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'options' => 'required|min:1',
            'options.*' => 'required',
            'captcha' => 'required'
        ], $custom_error_message);

        if ($validator->fails())
        {
            $errors = $validator->errors();
            return response($errors, 400);
        }

        $poll = Poll::create([
            'question' => $request->input('question'),
            'captcha' => $request->input('captcha'),
        ]);

        foreach ($request->input('options') as $i => $option)
        {
            Option::create([
                'poll_id' => $poll->id,
                'text' => $option,
                'position' => $i,
            ]);
        }

        return response(['poll' => Poll::find($poll->id) ], 200);
    }

    /**
     * Edits an existing poll.
     *
     * @param Request $request
     * @param UUID $id
     * @return void
     */
    public function edit(Request $request, $id)
    {
        dd($id, $request);
    }
}
