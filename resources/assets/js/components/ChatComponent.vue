<template>

    <div v-bind:id="'chat-component-' + this.index"  class="chat" v-show="isActive">

        <div class="messages" v-bind:id="'messages-' + this.index">

            <div class="loading" v-show="this.loading">
                <i class="fa fa-spinner fa-spin"></i> <span>Loading</span>
            </div>

            <div v-show="this.noMessages" class="no-messages alert alert-info" role="alert">
                <span>No messages yet</span>
            </div>

            <ul v-chat-scroll="{always: false, smooth: true}" v-on:scroll="onScroll">
                <li v-for="message in this.messages">

                    <message-component :message="message" :user="user" :key="message.arrived_at"></message-component>

                </li>
            </ul>
        </div>
        <div class="wrapper">
            <div class="send_box">
                <span class="notifications" v-if="activePeer !==false" v-text="activePeer + ' is typing'"></span>
                <textarea :id="'textarea-' + this.index" v-on:keydown="typing_notification"
                          v-on:keydown.enter.exact.prevent="pressSend"
                          v-on:keyup.enter.exact.prevent="releaseSend" rows="1" class="form-control"
                          placeholder="Enter you message here..." autofocus></textarea>
            </div>

            <div class="button_wrapper">
                <button type="button" v-bind:id="'send-' + this.index" v-on:click="send" v-on:keydown.enter="pressSend" v-on:keyup.enter="releaseSend"
                        class="btn btn-lg btn-info button">

                </button>
            </div>
        </div>
    </div>

</template>

<script>

    import autosize from 'autosize'

    import moment from 'moment'

    import vueChatScroll from 'vue-chat-scroll'

    import swal from 'sweetalert'

    export default {

        props: {
            user: {
                type: String,
                required: true,
            },
            selected: {
                type: Boolean,
                default: false,
            },
            index: {
                type: Number,
                required: true
            },
            room: {
                type: Object,
                required: true,
            },
        },

        data() {
            return {
                messages: [],
                loading: false,
                nextPageUrl: '',
                activePeer: false,
                typingTimer: false,
                isActive: null,
                newMessage: false,
            }
        },

        computed: {

            noMessages: function () {

                return this.messages.length === 0 && this.loading === false;

            }

        },

        created () {

            this.nextPageUrl = 'rooms/' + this.room.id + '/messages/1';

            this.fetchMessages();

            window.Echo.join('messages-channel.' + this.room.id)
                .here((users) => {

                    let roomUsers = {
                        "id": this.room.id,
                        "name": this.room.name,
                        "users": users
                    };

                    Bus.$emit('usersHere', roomUsers);
                })

                .joining((user) => {

                    let joiningUser = {
                        "id": this.room.id,
                        "name": this.room.name,
                        "user": {"name": user.name, "avatar": user.avatar, "is_online": user.is_online}
                    };

                    Bus.$emit('userJoining', joiningUser);
                })

                .leaving((user) => {

                    let leavingUser = {
                        "id": this.room.id,
                        "name": this.room.name,
                        "user": {"name": user.name, "avatar": user.avatar, "is_online": 'Offline'}
                    };

                    Bus.$emit('userLeaving', leavingUser);

                })

                .listen('IncomingMessage', (e) => {
                    this.activePeer = false;
                    this.messages.push(e.message);


                    if (!this.isActive) {
                        this.newMessage = true;

                    }
                })
                .listenForWhisper('typing', this.flash_notification);
//            }

        },
        mounted () {

            this.isActive = this.selected;

            const ta = document.getElementById('textarea-' + this.index);
            autosize(ta);

            ta.style.resize = 'none';
            ta.style.scroll = 'none';

        },

        methods: {

            onScroll(event) {

                const wrapper = event.target;

                let scrollTop = wrapper.scrollTop;

                if (scrollTop === 0) {
                    this.fetchMessages();
                }

            },

            fetchMessages() {

                if (this.nextPageUrl !== null) {

                    this.loading = true;

                    axios.get(this.nextPageUrl)
                        .then(response => {

                            if (response.status === 200)
                            {

                                let temp = this.messages;

                                this.messages = response.data.data.reverse().concat(temp);

                                this.nextPageUrl = response.data.next_page_url;

                            }

                            this.loading = false;

                    })
                        .catch( (error) => {

                            this.loading = false;
                        });
                }

            },

            pressSend() {
                document.getElementById('send-' + this.index).classList.add('is-active');
                this.send();
            },

            send(){

                const ta = document.getElementById('textarea-' + this.index);

                let url = '/rooms/messages/add', messageObject = {roomName: this.room.name, body: ta.value};

                if (ta.value !== '') {

                    let self = this;

                    function axiosWrapper (url, messageObject, ta) {

                        self.messages.push(messageObject.body);

                        axios.post(url, messageObject)
                            .then(function(response){

                            })
//                            .bind(messageObject))
                            .catch(function(error) {
                                self.messages.pop();

                                let errorText = (error.response.status === 403 ? error.response.data : "Please, try again later");

                                swal({
                                    title: "An error has occurred while posting your message!",
                                    text: errorText,
                                    icon: "error",
                                    closeModal: false,
                                }).then(() => {
                                    ta.focus();
                                });

                            });

                        ta.value = '';

                        autosize.update(ta);
                    }

                    axiosWrapper(url, messageObject, ta);

                }
            },

            releaseSend(){
                document.getElementById('send-' + this.index).classList.remove('is-active');
            },

            typing_notification: function () {

                window.Echo.join('messages-channel.' + this.room.id)
                    .whisper('typing', {
                        name: JSON.parse(this.user).name
                    });

            },

            flash_notification: function (e) {

                this.activePeer = e.name;

                if (this.typingTimer) clearTimeout(this.typingTimer);

                this.typingTimer = setTimeout(() => {
                    this.activePeer = false;
                }, 1000);
            }

        },

    }

</script>
