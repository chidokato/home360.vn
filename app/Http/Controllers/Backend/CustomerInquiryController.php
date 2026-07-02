<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CustomerInquiry;

class CustomerInquiryController extends Controller
{
    public function index()
    {
        $inquiries = CustomerInquiry::query()
            ->with('post.category')
            ->latest()
            ->paginate(20);

        return view('backend.customer-inquiries.index', compact('inquiries'));
    }
}
