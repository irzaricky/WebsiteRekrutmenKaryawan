@extends('layouts.master')
@section('content')
    <!-- Page-content -->
    <div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">View Deatil Leave (Employee)</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="#!" class="text-slate-400 dark:text-zink-200">Leaves Manage</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">Deatil Leave (Employee)</li>
                </ul>
            </div>
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-x-5">
                <div class="xl:col-span-9">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-4 text-15 grow">Deatil Leave</h6>
                            <form id="applyLeave">
                                @csrf
                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-12">
                                    <div class="xl:col-span-6">
                                        <div>
                                            <label for="leaveType" class="inline-block mb-2 text-base font-medium">Leave Type</label>
                                            <input type="text" name="" id="" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" value="{{ $leaveDetail->leave_type }}" disabled>
                                        </div>
                                    </div>
                                    <div class="xl:col-span-6">
                                        <div>
                                            <label for="remainingLeaves" class="inline-block mb-2 text-base font-medium">Remaining Leaves</label>
                                            <input type="text" name="" id="" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" value="{{ $leaveDetail->remaining_leave }}" disabled>
                                        </div>
                                    </div>
                                    <div class="xl:col-span-6">
                                        <label for="fromInput" class="inline-block mb-2 text-base font-medium">From</label>
                                        <input type="text" name="" id="" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" value="{{ $leaveDetail->date_from }}" disabled>
                                    </div>
                                    <div class="xl:col-span-6">
                                        <label for="date_to" class="inline-block mb-2 text-base font-medium">To</label>
                                        <input type="text" name="" id="" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" value="{{ $leaveDetail->date_to }}" disabled>
                                    </div>
                                    @foreach($leaveDate as $key => $date)
                                        <div class="xl:col-span-6">
                                            <label for="leave_date_{{ $key }}" class="inline-block mb-2 text-base font-medium">Leave Date {{ $key + 1 }}</label>
                                            <input type="text" id="leave_date_{{ $key }}" class="form-input border-slate-200 dark:border-zinc-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zinc-600 disabled:border-slate-300 dark:disabled:border-zinc-500 dark:disabled:text-zinc-200 disabled:text-slate-500 dark:text-zinc-100 dark:bg-zinc-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zinc-200" value="{{ $date }}" disabled>
                                        </div>
                                        <div class="xl:col-span-6">
                                            <label for="leave_day_{{ $key }}" class="inline-block mb-2 text-base font-medium">Leave Day {{ $key + 1 }}</label>
                                            <input type="text" id="leave_day_{{ $key }}" class="form-input border-slate-200 dark:border-zinc-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zinc-600 disabled:border-slate-300 dark:disabled:border-zinc-500 dark:disabled:text-zinc-200 disabled:text-slate-500 dark:text-zinc-100 dark:bg-zinc-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zinc-200" value="{{ $leaveDay[$key] ?? '' }}" disabled>
                                        </div>
                                    @endforeach
                                    <div class="xl:col-span-12">
                                        <div>
                                            <label for="number_of_day" class="inline-block mb-2 text-base font-medium">Number of Days</label>
                                            <input type="text" name="" id="" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" value="{{ $leaveDetail->number_of_day }}" disabled>
                                        </div>
                                    </div>
                                    <div class="md:col-span-2 xl:col-span-12">
                                        <div>
                                            <label for="reason" class="inline-block mb-2 text-base font-medium">Reason</label>
                                            <textarea name="reason" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" rows="3" disabled="">{{ $leaveDetail->reason }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 mt-4">
                                    <button class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20"><a href="{{ route('hr/leave/employee/page') }}">Back</a></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="xl:col-span-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-4 text-15">Leave Information ({{ date('Y') }})</h6>
                            <div>
                                <table class="w-full mb-0">
                                    <tbody>
                                        @foreach($leaveInformation as $key => $value)
                                            <tr>
                                                <td class="px-3.5 py-2.5 first:pl-0 last:pr-0 border-y border-transparent">{{ $value->leave_type }}</td>
                                                <th class="px-3.5 py-2.5 first:pl-0 last:pr-0 border-y border-transparent">{{ $value->leave_days }}</th>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('script')
  
@endsection
@endsection











