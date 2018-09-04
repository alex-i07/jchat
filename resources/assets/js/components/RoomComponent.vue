<template>

    <div v-show="showRoom">

        <ul class="list-group">

            <li v-for="(user, key, index) in users" class="wrap list-group-item">
                <img :src="'storage/users-avatars/' +  user.avatar" alt="Avatar">
                {{user.name}}

                <time-ago-component :created="user.is_online"></time-ago-component>

            </li>

        </ul>

    </div>

</template>

<script>

    export default {

        props: {
            roomUsers: {
                type: Array,
                required: true,
            },
            room: {
                type: Object,
                required: true,
            },
        },

        data () {
            return {
                showRoom: false,
                users: null,
            }

        },

        mounted() {

            this.users = this.roomUsers;

            Bus.$on('usersHere', (roomUsers) => {

                if (this.room.id == roomUsers.id) {

                    for(var i=0; i < this.users.length; i++)
                    {
                       let index = roomUsers.users.findIndex(value => {
                           return value.name === this.users[i].name;
                       });

                        if (index < 0) {

                            this.users[i].is_online = 'Offline';

                        }
                    }

                }

            });

            Bus.$on('userJoining', (joiningUser) => {

                if (this.room.id == joiningUser.id) {

                    for(let i = 0; i < this.users.length; i++) {

                        if(this.users[i].name === joiningUser.user.name) {
                            this.users[i].is_online = joiningUser.user.is_online;
                        }

                    }
                }

            });

            Bus.$on('userLeaving', (leavingUser) => {

                if (this.room.id == leavingUser.id) {

                    for (let i = 0; i < this.users.length; i++) {
                        if (this.users[i].name === leavingUser.user.name) {
                            this.users[i].is_online = leavingUser.user.is_online;
                        }
                    }
                }


            });

            Bus.$on('WebSocket-UserLeftRoom', (data) => {

                if (this.room.id == data.roomId) {

                    let index = this.users.findIndex((value) => {
                        return value.name === data.userName;
                    });

                    this.users.splice(index, 1);

                }
            })
        },

    }

</script>