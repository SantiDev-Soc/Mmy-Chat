<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContactController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');

        $contacts = User::where('id', '!=', auth()->id())
            ->where(function ($q) use ($query) {
                $q->where('name', 'ILIKE', "%{$query}%")->orWhere('email', 'ILIKE', "%{$query}%");
            })
            ->limit(20)->get(['id', 'name', 'email']);

        return response()->json($contacts);
    }
}
