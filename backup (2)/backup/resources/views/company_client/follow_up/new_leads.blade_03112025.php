@extends('layouts.client')
@section('title', 'New Lead List')
@section('content')

    <?php
    $profileId = Request::segment(2);
    $leadPipeline = App\Models\LeadPipeline::where([
        'slugname' => $profileId,
        'company_id' => Auth::user()->company_id,
    ])->first();
    ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">

                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    {{ $leadPipeline->pipeline_name }} List
                                </h5>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="">
                                        <div class="table-responsive">
                                            <table class=" table table-bordered table-striped table-hover datatable">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Sr No</th>
                                                        <th>Company Name</th>
                                                        <th>Contact Person Name</th>
                                                        <th>Email</th>
                                                        <th>Mobile</th>
                                                        <th>Service / Product</th>
                                                        @if (!in_array($status, ['deal-done']))
                                                            <th>Followup Date</th>
                                                        @endif
                                                        <th>Lead Source</th>
                                                        @if ($status === 'deal-done')
                                                            <th>Lead Done Date</th>
                                                        @endif
                                                        @if ($status === 'deal-cancel')
                                                            <th>Lead Cancel Date</th>
                                                        @endif
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $i = 1; ?>

                                                    @forelse($leads as $lead)
                                                        <tr class="text-center">
                                                            <td>{{ $i }}
                                                            </td>
                                                            <td>{{ $lead->company_name ?? '-' }}</td>
                                                            <td>{{ $lead->customer_name ?? '-' }}</td>
                                                            <td>{{ $lead->email ?? '-' }}</td>
                                                            <td>{{ $lead->mobile ?? '-' }}</td>
                                                            <td>{{ $lead->service_name ?? '-' }}</td>
                                                            @if (!in_array($status, ['deal-done']))
                                                                <td>{{ $lead->next_followup_date ?? '-' }}</td>
                                                            @endif
                                                            <td>
                                                                {{ $lead->lead_source_name ?? '' }}
                                                            </td>
                                                            @if ($status === 'deal-done')
                                                                <td>
                                                                    {{ $lead->deal_done_at ? date('d-m-Y H:i', strtotime($lead->deal_done_at)) : '-' }}
                                                                </td>
                                                            @endif
                                                            @if ($status === 'deal-cancel')
                                                                <td>
                                                                    {{ $lead->deal_cancel_at ? date('d-m-Y H:i', strtotime($lead->deal_cancel_at)) : '-' }}
                                                                </td>
                                                            @endif

                                                            @if ($profileId === 'new-lead')
                                                                <td>
                                                                    <a href="{{ route('clients.followup_detail', [$status, $lead->lead_id]) }}"
                                                                        class="btn btn-sm btn-success" title="Add Followup">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </td>
                                                            @else
                                                                {{--  @if ($leadPipeline->followup_needed == 'yes')  --}}
                                                                <td>
                                                                    <a href="{{ route('clients.followup_detail', [$status, $lead->lead_id]) }}"
                                                                        class="btn btn-sm btn-success" title="Add Followup">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </td>
                                                                {{--  @endif  --}}
                                                            @endif

                                                        </tr>
                                                        <?php $i++; ?>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">No Follow Up Found.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $leads->links() }}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
