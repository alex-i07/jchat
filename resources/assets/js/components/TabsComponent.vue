<template>

    <div class="outer-wrapper card border-top-0">

        <div class="inner-wrapper">

            <button v-show="showScrollButtons" v-on:mousedown="scrollTabs(false)" v-on:mouseup="endScroll"
                    v-on:keydown.enter="scrollTabs(false)" v-on:keyup.enter="endScroll" type="button" class="btn btn-outline-info left">
                <i class="fas fa-chevron-left"></i>
            </button>

            <div id="scrollable" class="btn-group">

                <a tabindex="0" v-for="(tab, index) in tabs" :key="tab.room.id" v-on:click="selectTab(tab)" v-on:keydown.enter="selectTab(tab)" class="btn btn-outline-lignt room-tab"
                   v-bind:class="{'selected': tab.isActive, 'new-message': tab.newMessage}">
                    <div>
                        <span>{{ tab.room.name}}</span>

                        <button tabindex="0" v-if="index !== 0" v-on:click="closeTab(tab.room.id, index)" type="button" data-toggle="tooltip" data-placement="top"
                                title="Delete yourself from this room and close it" class="close btn btn-sm custom-close" aria-label="Close">

                            <span aria-hidden="true">&times;</span>

                        </button>
                    </div>

                </a>

            </div>

            <button v-show="showScrollButtons" v-on:mousedown="scrollTabs(true)" v-on:mouseup="endScroll"
                    v-on:keydown.enter="scrollTabs(true)" v-on:keyup.enter="endScroll" type="button" class="btn btn-outline-info right">
                <i class="fas fa-chevron-right"></i>
            </button>

        </div>

        <div>

            <chat-component v-for="(room, index) in this.rooms" :key="room.id" :selected="index === 0 ? true: false"
                            v-bind:room="room"
                            v-bind:user="user"
                            v-bind:index="index"
            >
            </chat-component>

        </div>

    </div>

</template>

<script>

    import swal from 'sweetalert';

    export default {
        props: {
            user: {
                type: String,
                required: true,
            },
            initialRooms: {
                type: String,
                required: true,
            },
        },

        data() {
            return {
                tabs: [],
                rooms: [],
                scrolling: false,
                showScrollButtons: false,
                scrollWidth: 0,
                timeoutId: null,
            }
        },

        created() {

            this.tabs = this.$children;

            this.rooms = JSON.parse(this.initialRooms);

        },

        mounted() {

            Bus.$on ('CreatedRoomEvent', (newRoom) => {

                this.rooms.push({id: newRoom.id, name: newRoom.name});

            });

            Bus.$on ('NewRoomWebsocketAnnouncement', (newRoom) => {

                this.rooms.push({id: newRoom.id, name: newRoom.name});

            });

        },

        updated() {

            this.scrollWidth = document.getElementById('scrollable').scrollWidth;

        },

        watch: {
            scrollWidth: function (value) {

                let clientWidth = document.getElementById('scrollable').clientWidth;

                if (this.scrollWidth === clientWidth) {
                    this.showScrollButtons = false;
                }

                if (this.scrollWidth > clientWidth) {
                    this.showScrollButtons = true;
                }
            }
        },

        methods: {
            selectTab(selectedTab) {
                this.tabs.forEach(tab => {

                    if (tab.room.name === selectedTab.room.name) {

                        tab.isActive = true;

                        if (tab.newMessage) {

                            tab.newMessage = false;
                        }
                    }

                    else {
                        tab.isActive = false;
                    }
                });
            },

            closeTab(roomId, index) {

                axios.get('/rooms/close/' + roomId)
                    .then( (response) => {

                        Bus.$emit('RoomClosing', roomId);

                        window.Echo.leave('messages-channel.' + roomId);

                        this.rooms.splice(index, 1);
                        this.tabs.splice(index, 1);

                        this.$children[0].isActive = true;

                    })
                    .catch(function (error) {
                        swal("An error has occurred while closing a room!", "Please, try again later", "error");
                    });

            },

            scrollTabs(direction) {

                this.scrolling = true;

                let scrollValue = direction ? 50 : -50;

                var self = this;

                function smoothScrolling() {

                    const div = document.getElementById("scrollable");

                    let currentScrollValue = div.scrollLeft;

                    div.scrollLeft = currentScrollValue + scrollValue;

                    if (self.scrolling && ( ((currentScrollValue + scrollValue) >= 0) && ((currentScrollValue + scrollValue) <= (div.scrollWidth - div.clientWidth)) ))

                        this.timeoutId = setTimeout(smoothScrolling, 25);
                }

                this.timeoutId = setTimeout(smoothScrolling, 25);

            },

            endScroll() {
                this.scrolling = false;
            }
        },

        destroyed() {
            clearTimeout(this.timeoutId);
        }
    }

</script>