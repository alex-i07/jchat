<template>

    <div class="rooms">

        <div class="card">

            <div class="card-header">
                <span>All rooms</span>
            </div>

            <div class="just-padding">

                <div class="list-group list-group-root">

                    <a tabindex="0" v-for="(room, index) in this.rooms_participants" :key="room.id"
                       v-on:click="list(room, index)" v-on:keydown.enter="list(room, index)" class="list-group-item">

                        <i :id="'fa-icon-' + index" class="fa fa-caret-right highlight"></i>
                        <span class="highlight">{{room.name}}</span>
                        <span v-if="room.users.length > 1" class="badge badge-primary badge-pill float-right mt-1">{{room.users.length}} users, {{ onlineUsersCounter(room)}} online</span>
                        <span v-if="room.users.length === 1" class="badge badge-primary badge-pill float-right mt-1">{{room.users.length}} user, {{ onlineUsersCounter(room)}} online</span>

                        <room-component :room-users="room.users" :room="{'id': room.id, 'name': room.name}" :key="room.id"></room-component>

                    </a>

                </div>

            </div>

        </div>
    </div>

</template>

<script>

    export default {
        props: {
            user: {
                type: String,
                required: true,
            },
            rooms: {
                type: String,
                required: true,
            },
        },

        data() {
            return {
                rooms_participants: [],
                children: [],
            }
        },

        created () {
            this.children = this.$children;

        },

        mounted () {

            this.rooms_participants = JSON.parse(this.rooms);

            Bus.$on('CreatedRoomEvent', newRoom => {
                this.rooms_participants.push(newRoom);
            });

            Bus.$on('RoomClosing', (roomId) => {

                let index = this.rooms_participants.findIndex((item) => {

                    return item.id == roomId;
                });

                this.rooms_participants.splice(index, 1);

            });

            window.Echo.private('user.' + JSON.parse(this.user).id)
                .listen('NewRoomCreated', (e) => {

                    Bus.$emit('NewRoomWebsocketAnnouncement', e.room);

                    this.rooms_participants.push(e.room);
                })
                .listen('UserLeftRoom', (e) => {

                    Bus.$emit('WebSocket-UserLeftRoom', e.data);


                });

        },

        methods: {

            list(selectedRoom, index) {

                const fa = document.querySelector('#fa-icon-' + index);

                fa.className = (fa.className === 'fa fa-caret-right highlight' ? 'fa fa-caret-down highlight' : 'fa fa-caret-right highlight');

                this.children[index].showRoom =!this.children[index].showRoom;
            },

            onlineUsersCounter (room) {

                let onlineUsers = room.users.filter (user => {
                    return user.is_online !=='Offline';
                });

                return onlineUsers.length;
            }
        }
    }


</script>