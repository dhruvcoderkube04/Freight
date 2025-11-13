@extends('admin.layouts.base')

@section('title', 'Payment Requests - Admin')

@section('content')
<div id="kt_app_content_container" class="app-container">
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold">Payment Requests</h3>
            </div>
            <div class="card-toolbar">
                <div class="d-flex align-items-center position-relative my-1 me-5">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" id="payment_search"
                           class="form-control form-control-solid w-250px ps-13"
                           placeholder="Search requests..." />
                </div>

                <button type="button" class="btn btn-light-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_export_requests">
                    <i class="ki-duotone ki-exit-up fs-2"><span class="path1"></span><span class="path2"></span></i>
                    Export
                </button>
            </div>
        </div>

        <div class="card-body py-4">
            <table class="table align-middle table-row-dashed fs-6 gy-5"
                   id="kt_datatable_payment_requests">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-50px">SR NO</th>
                        <th class="min-w-180px">User</th>
                        <th class="min-w-100px">Quote ID</th>
                        <th class="min-w-200px">Route</th>
                        <th class="min-w-150px">Carrier</th>
                        <th class="min-w-120px">Amount</th>
                        <th class="min-w-120px">Requested</th>
                        <th class="min-w-100px text-center">Status</th>
                        <th class="min-w-150px text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="kt_modal_export_requests" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Export Payment Requests</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="export_form_requests">
                    <div class="mb-10">
                        <label class="form-label">Select Format</label>
                        <select class="form-select" id="export_format_requests" data-control="select2">
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Export Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Note Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="rejectForm">
                @csrf
                <input type="hidden" name="status" value="rejected">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Payment Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Admin Note <small class="text-muted">(optional)</small></label>
                        <textarea name="admin_note" class="form-control" rows="3"
                                  placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let table;
const updateUrl = '{{ route('admin.payment-requests.update-status', ':id') }}';
const rejectModal = new bootstrap.Modal('#rejectModal');

$(document).ready(function () {
    table = $('#kt_datatable_payment_requests').DataTable({
        dom: "<'row my-5'<'col-12 d-flex justify-content-between'<'d-flex align-items-center position-relative my-1'f><'datatable-buttons'B>>>" +
             "<'table-responsive'tr>" +
             "<'row d-flex align-items-center justify-content-between'<'col d-flex align-items-center gap-3'l i><'col-auto'p>>",
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.payment-requests.data") }}',
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        },
        columns: [
            { data: 'sr_no', className: 'text-center' },
            { data: 'user', orderable: false },
            { data: 'quote_id', orderable: false },
            { data: 'route', orderable: false },
            { data: 'carrier', orderable: false },
            { data: 'amount', orderable: false },
            { data: 'requested', orderable: false },
            { data: 'status', orderable: false, className: 'text-center' },
            { data: 'actions', orderable: false, className: 'text-center' }
        ],
        language: { processing: '<div class="spinner-border text-primary"></div>' },
        buttons: []
    });

    $('#payment_search').on('keyup', function () {
        table.search(this.value).draw();
    });

    $('#export_form_requests').on('submit', function (e) {
        e.preventDefault();
        const format = $('#export_format_requests').val();
        const cfg = {
            excel: { extend: 'excel', title: 'Payment_Requests' },
            csv:   { extend: 'csv',   title: 'Payment_Requests' },
            pdf:   { extend: 'pdf',   title: 'Payment_Requests' }
        };
        table.button().add(0, cfg[format]);
        table.button(0).trigger();
        table.button().remove();
        toastr.success(`Exported as ${format.toUpperCase()}`);
        $('#kt_modal_export_requests').modal('hide');
    });

    // APPROVE WITH SWEETALERT2
    $(document).on('click', '.btn-approve', function () {
        const id = $(this).data('id');
        const $btn = $(this);

        Swal.fire({
            title: 'Approve Payment Request?',
            text: 'User will be able to proceed with payment.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve!',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                $btn.prop('disabled', true);
                $.post(updateUrl.replace(':id', id), {
                    _token: '{{ csrf_token() }}',
                    status: 'approved'
                })
                .done(res => {
                    if (res.success) {
                        Swal.fire('Approved!', res.message, 'success');
                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', res.message || 'Failed to approve', 'error');
                    }
                })
                .fail(() => Swal.fire('Error', 'Server error', 'error'))
                .always(() => $btn.prop('disabled', false));
            }
        });
    });

    // REJECT - Open Modal First
    $(document).on('click', '.btn-reject', function () {
        const id = $(this).data('id');
        $('#rejectForm').data('url', updateUrl.replace(':id', id));
        $('#rejectForm')[0].reset();
        rejectModal.show();
    });

    // REJECT - Submit with SweetAlert Confirmation
    $('#rejectForm').on('submit', function (e) {
        e.preventDefault();
        const url = $(this).data('url');
        const note = $('textarea[name="admin_note"]').val();
        const text = note ? `Reason: ${note}` : 'No reason provided.';

        Swal.fire({
            title: 'Reject Payment Request?',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, reject!',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(url, $(this).serialize())
                .done(res => {
                    if (res.success) {
                        Swal.fire('Rejected!', res.message, 'success');
                        rejectModal.hide();
                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', res.message || 'Failed to reject', 'error');
                    }
                })
                .fail(() => Swal.fire('Error', 'Server error', 'error'));
            }
        });
    });
});
</script>
@endpush