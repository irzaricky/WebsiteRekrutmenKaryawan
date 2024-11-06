<?php

namespace App\Http\Controllers;

use App\Models\HrdAction;
use Inertia\Inertia;
use Illuminate\Http\Request;

class HRDHistoryController extends Controller
{
    public function index()
    {
        $actions = HrdAction::with('user', 'testResult')->paginate(10);

        return Inertia::render('SubDashboard/history', [
            'title' => 'History',
            'actions' => $actions,
        ]);
    }
}