<template>

    <div class="card">
        <div class="card-header">Register</div>

        <div class="card-body">
            <form id="dropzone" class="dropzone" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="_token" :value="csrf">

                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>

                    <div class="col-md-6">
                        <input v-on:keyup.enter="register" id="name" type="text" class="form-control" v-bind:class="{'is-invalid': invalidName}" name="name" required autofocus>

                        <span v-if="invalidName" class="invalid-feedback">
                                        <strong>{{ errors.name[0]}}</strong>
                                    </span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>

                    <div class="col-md-6">
                        <input v-on:keyup.enter="register" id="email" type="email" class="form-control" v-bind:class="{'is-invalid': invalidEmail}" name="email" required>

                        <span class="invalid-feedback">
                                        <strong v-if="invalidEmail">{{ errors.email[0] }}</strong>
                                    </span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

                    <div class="col-md-6">
                        <input v-on:keyup.enter="register" id="password" type="password" class="form-control" v-bind:class="{'is-invalid': invalidPassword}" name="password" required>

                        <span class="invalid-feedback">
                                        <strong v-if="invalidPassword">{{ errors.password[0] }}</strong>
                                    </span>

                    </div>
                </div>

                <div class="form-group row">
                    <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirm Password</label>

                    <div class="col-md-6">
                        <input v-on:keyup.enter="register" id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">Your avatar (optional)</label>

                    <div class="col-md-6">
                        <div class="dz-container">

                            <div class="dropzone-previews form-control custom" v-bind:class="{'is-invalid': invalidAvatar}">
                                <div class="dz-message">Use buttons or drag & drop</div>
                            </div>

                            <div class="line"></div>

                            <div class="buttons-wrapper">

                                <button type="button" id="add" class="btn btn-success btn-sm add">
                                    <i class="fa fa-upload"></i>

                                </button>

                                <button v-on:click="removeInfo" type="button" id="remove" class="btn btn-danger btn-sm remove">
                                    <i class="fa fa-trash"></i>

                                </button>

                            </div>

                        </div>

                        <span class="invalid-feedback">
                                        <strong v-if="invalidAvatar">{{ errors.avatar[0] }}</strong>
                                    </span>

                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="button" id="apply" v-on:click="register" class="btn btn-lg btn-outline-primary custom-primary-button">
                            Register
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</template>

<script>

    import Dropzone from 'dropzone';

    import swal from 'sweetalert';

    window.Dropzone = Dropzone;
    window.Dropzone.autoDiscover = false;

    export default {

        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                errors: '',
                dropzone: null,
                thumbnail: null,
            }
        },

        computed: {
            invalidName: function () {
                return this.errors.hasOwnProperty('name') ? true : false;
            },

            invalidEmail: function () {
                return this.errors.hasOwnProperty('email') ? true : false;
            },

            invalidPassword: function () {
                return this.errors.hasOwnProperty('password') ? true : false;
            },

            invalidAvatar: function () {
                return this.errors.hasOwnProperty('avatar') ? true : false;
            }
        },

        mounted() {

            let self = this;

            this.dropzone = new Dropzone("#dropzone", {
                url: "register",
//                autoQueue: false,
                previewsContainer: ".dropzone-previews",
                clickable: "#add",
                paramName: "avatar",
                autoProcessQueue: false,
                uploadMultiple: false,
                maxFiles: 2,
                maxFilesize: 3,
                resizeWidth: 300,
                resizeHeight: 300,
                dictFileTooBig: 'Only files less than 3 Mb are allowed',
                previewTemplate: "<div class=\"dz-preview dz-file-preview\">\n  " +
                                "<div class=\"dz-image\"><img data-dz-thumbnail /></div>\n  " +
                                "<div class=\"dz-details\">\n    " +
                                "<div class=\"dz-size\"><span data-dz-size></span></div>\n    " +
                                "<div class=\"dz-filename\"><span data-dz-name></span></div>\n  " +
                                "</div>\n  " +
                                "<div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n  ",
                acceptedFiles: 'image/*',

                init: function() {
                    let dzClosure = this;

//                    let removeImageButton = document.querySelector("#remove");

                    this.on("addedfile", function(file) {

                        if (typeof this.files[1] !== 'undefined'){
                            this.removeFile(this.files[0]);

                        }
                        document.querySelector(".dz-message").style.display = 'none';

//                        removeImageButton.addEventListener("click", self.removeInfo);
                    });

                    this.on('thumbnail', function(file, dataURL) {

                        self.thumbnail = dataURL;

                    });

                },

            });

        },

        methods: {

            removeInfo: function() {

                this.dropzone.removeAllFiles(true);

                document.querySelector(".dz-message").innerHTML = 'Use buttons or drag & drop';
                document.querySelector(".dz-message").style.display = 'block';
            },

            register: function(event) {

                const form = document.querySelector("#dropzone");
                var formData = new FormData(form);

                ///////////////////////////////https://stackoverflow.com/questions/4998908/convert-data-uri-to-file-then-append-to-formdata

//                class ImageDataConverter {
//                    constructor(dataURI) {
//                        this.dataURI = dataURI;
//                    }
//
//                    getByteString() {
//                        let byteString;
//                        if (this.dataURI.split(',')[0].indexOf('base64') >= 0) {
//                            byteString = atob(this.dataURI.split(',')[1]);
//                        } else {
//                            byteString = decodeURI(this.dataURI.split(',')[1]);
//                        }
//                        return byteString;
//                    }
//
//                    getMimeString() {
//                        return this.dataURI.split(',')[0].split(':')[1].split(';')[0];
//                    }
//
//                    convertToTypedArray() {
//                        let byteString = this.getByteString();
//                        let ia = new Uint8Array(byteString.length);
//                        for (let i = 0; i < byteString.length; i++) {
//                            ia[i] = byteString.charCodeAt(i);
//                        }
//                        return ia;
//                    }
//
//                    dataURItoBlob() {
//                        let mimeString = this.getMimeString();
//                        let intArray = this.convertToTypedArray();
//                        return new Blob([intArray], {type: mimeString});
//                    }
//                }

                ////////////////////////////

                if (this.dropzone.getQueuedFiles().length > 0) {

//                    const blob = new ImageDataConverter(this.thumbnail).dataURItoBlob();

                    formData.append(this.dropzone.options.paramName, this.thumbnail);
//                    this.dropzone.files[0].dataURL = this.thumbnail;
//
//                    formData.append(this.dropzone.options.paramName, blob, this.dropzone.files[0].name);

                }

                axios.post('/register', formData)
                    .then((response) => {
                        if (response.status === 201) {
                            window.location = '/home';
                        }
                    })
                    .catch((error) => {
                        if (error.response.data.errors) {
                            this.errors = error.response.data.errors;
                        }
                        if (error.response.status !== 422) {
                            swal("An error has occurred while registering!", "Please, try again later", "error");
                        }
                    })
                ;

            }
        },

        destroyed () {
//            document.querySelector("#remove").removeEventListener('click', this.removeInfo);
        }
    }

</script>