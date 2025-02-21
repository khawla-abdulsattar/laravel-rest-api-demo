<?php
namespace App\Filters;

use Illuminate\Http\Request;

class PostFilter
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($query)
    {
        if ($this->request->has('author_id')) {
            $query->where('user_id', $this->request->author_id);
        }

        return $query;
    }
}
