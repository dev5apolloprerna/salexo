@extends('layouts.app')

@section('title', 'View Bus Details')

@section('content')

      <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Bus Details
                                    <a href="{{ route('admin.bus.index') }}" style="float: right;" class="btn btn-sm btn-primary">
                                        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
                                    </a>

                                </h5>
                                
                            </div>
                             <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class=" table table-bordered table-striped table-hover datatable">
                                                        <tr>
                                                            <th>
                                                                Bus Name
                                                            </th>
                                                            <td>
                                                                {{ $bus->busName ?? '' }}
                                                            </td>
                                                        </tr>
                                                         <tr>
                                                            <th>
                                                               Company Name
                                                            </th>
                                                            <td>
                                                            @foreach ($company as $value)
                                                            @if ($bus->companyId == $value->companyId)
                                                            {{ $value->companyName ?? '' }}
                                                                @endif
                                                            @endforeach

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>
                                                               Driver Name
                                                            </th>
                                                            <td>
                                                            @foreach ($driver as $value)
                                                            @if ($bus->driverIdForBus == $value->driverId)
                                                            {{ $value->driverName ?? '' }}
                                                                @endif
                                                            @endforeach

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>
                                                                Permit Expiry
                                                            </th>
                                                            <td>
                                                                @if(!empty($bus->permitExpiry))
                                                                     {{ date('d-m-Y',strtotime($bus->permitExpiry)) }}
                                                                @else
                                                                    {{ '-' }}
                                                                @endif
                                                            </td>
                                                        </tr> 
                                                        <tr>
                                                            <th>
                                                                Fitness Expiry
                                                            </th>
                                                            <td>
                                                                 @if(!empty($bus->fitnessExpiry))
                                                                     {{ date('d-m-Y',strtotime($bus->fitnessExpiry)) }}
                                                                @else
                                                                    {{ '-' }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>
                                                                Tax Expiry
                                                            </th>
                                                            <td>
                                                               @if(!empty($bus->taxExpiry))
                                                                     {{ date('d-m-Y',strtotime($bus->taxExpiry)) }}
                                                                @else
                                                                    {{ '-' }}
                                                                @endif

                                                            </td>
                                                        </tr> 
                                                        <tr>
                                                            <th>
                                                                Insurance Expiry
                                                            </th>
                                                            <td>
                                                               @if(!empty($bus->InsuranceExpiry))
                                                                     {{ date('d-m-Y',strtotime($bus->InsuranceExpiry)) }}
                                                                @else
                                                                    {{ '-' }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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