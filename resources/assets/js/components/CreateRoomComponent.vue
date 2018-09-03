<template>

    <div class="card">

        <button type="button" v-on:click="formBringer()" class="btn btn-info">
            <i class="fa" v-bind:class="{ 'fa-angle-double-right': right, 'fa-angle-double-down': down }"></i>
            <span>Create new room</span>
        </button>

        <div>
            <form class="px-4 py-3" v-if="formShow">
                <div class="form-group">

                    <label for="room-name">Enter name of the room</label>
                    <input type="text" class="form-control" id="room-name" v-model="roomName" placeholder="Room Name">

                </div>

                <div class="form-group">

                    <label for="online-users">All online users</label>
                    <select v-model="select" class="dropdown" id="online-users" ref="select" data-live-search="true" actionsBox="true"
                            noneSelectedText="Select users for your room" multiple>

                        <option v-for="user in Array.from(this.users)">
                            {{ user }}</option>
                    </select>

                </div>

                <button type="submit" @click.prevent="createRoom" class="btn btn-success">

                    <i v-show="this.spin" class="fa fa-spinner fa-spin"></i>

                    <span>Create Room</span>

                </button>

            </form>
        </div>

    </div>

</template>

<script>

    import swal from 'sweetalert'

    export default {
        props: {
            self: {
                type: String,
                required: true,
            },
        },

        data() {
            return {
                roomName: '',
                users: null,
                formShow: false,
                myself: '',
                select: [],
                spin: false,
            }
        },

        computed: {
            right: function () {
                return (this.formShow === false ? true : false);
            },

            down: function () {
                return (this.formShow === true ? true : false);
            }
        },

        updated () {
            $(this.$refs.select).selectpicker('refresh');

            const fa = document.getElementsByClassName('fa fa-angle-double-right');

            fa.className = 'fa fa-angle-double-down';
        },

        mounted() {

            this.myself = JSON.parse(this.self);

            Bus.$on('usersHere', (roomUsers) => {

                if (roomUsers.name === 'All users') {

                    let mediator = [];

                    roomUsers.users.forEach(user => {
                        if (user.name !== this.myself.name)
                        mediator.push(user.name);
                    });

                    this.users = new Set(mediator);

                    this.$forceUpdate();

                }

            });

            Bus.$on('userJoining', (joiningUser) => {

                if (joiningUser.name === 'All users')
                {
                    this.users.add(joiningUser.user.name);
                }

                this.$forceUpdate();

            });

            Bus.$on('userLeaving', (leavingUser) => {

                if (leavingUser.name === 'All users')
                {
                    this.users.forEach((value) => {
                        if (value.name === leavingUser.user.name) {
                            this.users.delete(leavingUser.user.name);
                        }
                    });
                }

                this.$forceUpdate();

            });
        },

        methods: {
            formBringer() {

                this.formShow = (this.formShow === false ? true : false);


            },

            createRoom() {

                this.spin = true;

                if (this.roomName === '') {
                    this.spin = false;
                    swal("No empty room names are allowed!", "Please, enter room name", "error");
                }

                else if(this.select.length < 1) {

                    this.spin = false;
                    swal("No empty rooms are allowed!", "Please, choose users for your room", "error");

                }

                else {

                    axios.post('/rooms/add', {name: this.roomName, users: this.select})
                        .then((response) => {

                            let newRoom = response.data;

                            Bus.$emit('CreatedRoomEvent', newRoom);

                            swal("Done!", "New room " + this.roomName + " was successfully created!", "success");

                            this.roomName = '';

                            this.spin = false;

                        })
                        .catch((error) => {
                            this.spin = false;

                            swal("An error has occurred while adding a room!", "Please, try again later", "error");
                        })
                    ;
                }

            }
        }
    }
</script>