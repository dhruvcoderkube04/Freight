@extends('admin.layouts.base')
@section('title', 'Customers')

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold">Customer List</h3>
            </div>
            <div class="card-toolbar">
                <!-- Search Box - Exact Design -->
                <div class="d-flex align-items-center position-relative my-1 me-5">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" id="customer_search"
                           class="form-control form-control-solid w-250px ps-13"
                           placeholder="Search customer" />
                </div>

                <!-- Export Button (opens modal) -->
                <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_export">
                    <i class="ki-duotone ki-exit-up fs-2"><span class="path1"></span><span class="path2"></span></i>
                    Export
                </button>
            </div>
        </div>

        <div class="card-body py-4">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_datatable_customer_list">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-50px">SR NO</th>
                        <th class="min-w-180px">Customer</th>
                        <th class="min-w-150px">Email</th>
                        <th class="min-w-120px">Registration Type</th>
                        <th class="min-w-120px">Joined</th>
                        <th class="min-w-100px text-center">Status</th>
                        <th class="min-w-100px text-center">Auto Approval</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="kt_modal_export" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Export Customers</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="export_form">
                    <div class="mb-10">
                        <label class="form-label">Select Format</label>
                        <select class="form-select" id="export_format" data-control="select2">
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
@endsection

@push('scripts')
<script>
const toggleUrl = '{{ route("admin.users.toggle-approval", ":user") }}';
let table;

$(document).ready(function () {
    table = $('#kt_datatable_customer_list').DataTable({
        dom: "<'row my-5'<'col-12 d-flex justify-content-between'<'d-flex align-items-center position-relative my-1'f><'datatable-buttons'B>>>" +
             "<'table-responsive'tr>" +
                "<'row d-flex align-items-center justify-content-between' \
                    <'col d-flex align-items-center gap-3'l i> \
                    <'col-auto'p> \
                >",
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.users.data") }}',
        columns: [
            { data: 'sr_no', className: 'text-center' },
            { data: 'customer', orderable: false },
            { data: 'email' },
            { data: 'type', orderable: false },
            { data: 'joined' },
            { data: 'status', orderable: false, className: 'text-center' },
            { data: 'approval', orderable: false, className: 'text-center' }
        ],
        language: { processing: '<div class="spinner-border text-primary"></div>' },
        buttons: [] // We control export manually
    });

    // Live Search
    $('#customer_search').on('keyup', function () {
        table.search(this.value).draw();
    });

    // Export Modal Submit
    $('#export_form').on('submit', function (e) {
        e.preventDefault();
        const format = $('#export_format').val();

        const buttonConfig = {
            excel: { extend: 'excel', title: 'Customers_List' },
            csv:   { extend: 'csv',   title: 'Customers_List' },
            pdf:   { extend: 'pdf',   title: 'Customers_List' }
        };

        table.button().add(0, buttonConfig[format]);
        table.button(0).trigger();
        table.button().remove();

        toastr.success(`Exported as ${format.toUpperCase()}`);
        $('#kt_modal_export').modal('hide');
    });

    // Toggle Approval
    $(document).on('change', '.user-approval-toggle', function () {
        const $this = $(this);
        const userId = $this.data('id');
        const isChecked = $this.is(':checked');
        const $row = $this.closest('tr');

        $this.prop('disabled', true);

        $.post(toggleUrl.replace(':user', userId), {
            _token: '{{ csrf_token() }}',
            status: isChecked
        })
        .done(res => {
            if (res.success) {
                toastr.success(res.message);
                $row.find('td:eq(5)').html(isChecked
                    ? '<span class="badge badge-success">Approved</span>'
                    : '<span class="badge badge-warning">Pending</span>'
                );
            } else {
                $this.prop('checked', !isChecked);
                toastr.error(res.message);
            }
        })
        .fail(() => {
            $this.prop('checked', !isChecked);
            toastr.error('Server error');
        })
        .always(() => $this.prop('disabled', false));
    });
});
</script>
@endpush