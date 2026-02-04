@section('modals')
    @parent
    <div class="modal fade" id="modal-file-upload" tabindex="-1" aria-labelledby="modal-file-upload-label" aria-hidden="true"
        style="z-index:2055;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-file-upload-label">Upload Profile Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="upload-input-container">
                        <input id="file-upload-input" type="file" onChange="inputFileUpdated()" accept="image/png, image/jpeg" />
                    </div>
                    <div class="upload-progress-container d-none">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
                        </div>
                    </div>
                    <div id="upload-input-error" class="alert alert-danger mt-2 d-none" role="alert">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-upload d-none" onclick="uploadFile()">Upload</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="module">
        var abortController;

        $(() => {
            $('#modal-file-upload').on('show.bs.modal',clearFile);
            $('#modal-file-upload').on('hide.bs.modal',function(){ 
                if(typeof(abortController) !== 'undefined')
                    abortController.abort();
                clearFile(); 
            });
        });
        window.uploadFile = function()
        {
            fileUploader.upload();
        }
        window.clearFile = function()
        {
            $('#file-upload-input').val('');
            $('#upload-input-error').html();

            $('#upload-input-error').addClass('d-none');
        }
        window.inputFileUpdated = function()
        {
            let e = document.getElementById('file-upload-input');
            if(e.files.length === 0)
                $('#modal-file-upload').find('.btn-upload').addClass('d-none');
            else
                $('#modal-file-upload').find('.btn-upload').removeClass('d-none');

        }
        window.fileUploader = {
            target: undefined,
            onUploadCompleted: undefined,
            upload: function()
            {
                let _this = this
                abortController = new AbortController();   
                
                let e = document.getElementById('file-upload-input');
                if(e.files.length === 0)
                    return;

                let file = e.files[0];
                let formData = new FormData();
                formData.set('file',file);
                formData.set('_token',"{{ csrf_token() }}");

                $('#modal-file-upload').find('.btn-upload').addClass('d-none');
                $('#modal-file-upload').find('.upload-input-container').addClass('d-none');
                $('#modal-file-upload').find('.upload-progress-container').removeClass('d-none');
                $('#modal-file-upload').find('.progress-bar').attr('aria-valuenow',0).attr('style','width:0%');
                

                axios.post(this.target,formData,{
                    signal: abortController.signal,
                    onUploadProgress : progressEvent => 
                    {
                        var pcg = Math.floor(progressEvent.loaded / progressEvent.total *100);        
                        $('#modal-file-upload').find('.progress-bar').attr('aria-valuenow',pcg).attr('style','width:'+Number(pcg)+'%');
                    }
                    })
                .then(res=>{
                    bootstrap.Modal.getInstance('#modal-file-upload').hide();
                    $('#modal-file-upload').find('.upload-progress-container').addClass('d-none');
                    $('#modal-file-upload').find('.upload-input-container').removeClass('d-none');

                    if(typeof(_this.onUploadCompleted) === 'function')
                        _this.onUploadCompleted(res.data);

                })
                .catch(function(res)
                {
                    
                    
                    $('#modal-file-upload').find('.btn-upload').removeClass('d-none');
                    $('#modal-file-upload').find('.upload-progress-container').addClass('d-none');
                    $('#modal-file-upload').find('.upload-input-container').removeClass('d-none');

                    $('#upload-input-error').html('');
                    
                    if(typeof(res.response) === 'object' && 
                        typeof(res.response.data) === 'object' && 
                        typeof(res.response.data.errors) === 'object' &&
                        Array.isArray(res.response.data.errors.file))
                    {
                        res.response.data.errors.file.forEach(err => {
                            let dv = $('<div></div>');
                            dv.html(err);
                            $('#upload-input-error').append(dv);    
                        });
                        $('#upload-input-error').removeClass('d-none');
                    }
                    else
                    {
                        $('#upload-input-error').addClass('d-none');
                    }
                });
            }
        };
    </script>
@endsection
