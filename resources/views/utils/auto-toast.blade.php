@section('templates')
@parent
<template id="toast-template">
    <div class="toast border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header border-0">
            <strong class="me-auto toast-title"></strong>
            <button type="button" class="btn-close toast-dismiss-button" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body toast-message opacity-75">
        </div>
    </div>
</template>
@endsection
@section('scripts')
@parent
<script>
    $(document).ready(function() {
        autoToast.initialize().showAll();
        
    });
    var autoToast = {
        toasts: <?php echo json_encode(App\Extras\Utils\ToastHelper::getToasts()); ?>,
        initialize: function() {
            $('body').append('<div class="toast-container position-absolute p-3 top-0 end-0"></div>'); //put toast-container
            return this;
        },
        createToastAlert: function(toast) {
            var e = $($("#toast-template").html());
            e.addClass('toast-' + toast.context);
            

            if (toast.title === null) {
                e.find('.toast-title').html(toast.message);
                e.find('.toast-body').addClass('d-none');
            } else {
                e.find('.toast-title').html(toast.title);
                e.find('.toast-message').html(toast.message);
            }

            
            if (!toast.dismissable) {
                e.find('.toast-dismiss-button').addClass('d-none');
            }
            return e;

        },
        show: function(toast) {
            var ta = this.createToastAlert(toast);
            $('.toast-container').append(ta);
            var bsToast = new bootstrap.Toast(ta, toast.options);
            bsToast.show();
            return this;
        },
        showAll: function() {
            var sender = this;
            this.toasts.forEach(toast => {
                sender.show(toast);
            });
        },
        clearAll: function()
        {
            $('.toast-container').html('');
        }
    };
</script>
@endsection