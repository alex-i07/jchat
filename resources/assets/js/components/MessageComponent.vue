<template>

    <div class="envelope" v-bind:class="this.messageData.direction">
        <div >
            <img :src="'http://jchat.local/storage/users__avatars/' + this.messageData.avatar" alt="Avatar"
                 v-bind:class="this.messageData.img">
        </div>

        <div class="message" v-bind:class="messageData.message_direction">
            <span v-if="messageData.show_nick" class="user_name">{{ messageData.name }}</span>

            <p v-bind:class="messageData.text">{{ messageData.body }}</p>

            <span class="time" v-bind:class="messageData.time">{{ time }}</span>
        </div>
    </div>

</template>

<script>

    import moment from 'moment'

    export default {

        props: {
            message: {
                type: [String, Object],
                required: true,
            },
            user: {
                type: String,
                required: true,
            },
        },

        data () {
            return {
                time: null,
                now: null,
                messageData: '',
                intervalId: null,
            }
        },

        created() {
            this.now = moment();

            this.message_handler(this.message);
        },

        mounted () {

            this.getTime();
            this.intervalId = setInterval(this.getTime, 60000);

        },

        methods: {

            message_handler: function (message) {

                if (typeof(message) === 'string')
                {
                    return this.messageData = { direction:'outgoing',
                        message_direction: 'message-outgoing',
                        img:'right',
                        avatar: JSON.parse(this.user).avatar,
                        name: JSON.parse(this.user).name,
                        show_nick: false,
                        text: 'right',
                        time:'time-left',
                        created: moment.utc().toString(),
                        body: message
                    };

                }
                if (typeof(message) === 'object')
                {

                    if (message.user.name === JSON.parse(this.user).name)
                    {
                        return this.messageData = { direction:'outgoing',
                            message_direction: 'message-outgoing',
                            img:'right',
                            avatar: JSON.parse(this.user).avatar,
                            name: message.user.name,
                            show_nick: false,
                            text: 'right',
                            time:'time-left',
                            created: message.arrived_at,
                            body: message.body
                        };

                    }

                    if (message.user.name !== JSON.parse(this.user).name)
                    {
                        return this.messageData = { direction:'incoming',
                            message_direction: 'message-incoming',
                            img:'left',
                            avatar: message.user.avatar,
                            name: message.user.name,
                            show_nick: true,
                            text: 'left',
                            time:'time-right',
                            created: message.arrived_at,
                            body: message.body
                        };
                    }
                }
            },

            getTime () {

                if (moment(this.messageData.created).isValid()) {

                    let utcToLocal = moment.utc(this.messageData.created).local();

                    let now = this.now;

                    moment.calendarFormat = (utcToLocal, now) => {

                        let diffHours = utcToLocal.diff(this.now, 'hours', true);

                        let diffDays = utcToLocal.diff(now, 'days', true);

                        return  diffHours >= -1 ? 'sameHour' :
                            diffDays < -6 ? 'sameElse' :
                                diffDays < -1 ? 'lastWeek' :
                                    diffDays < 0 ? 'lastDay' :
                                        diffDays < 1 ? 'sameDay' :
                                            diffDays < 2 ? 'nextDay' :
                                                diffDays < 7 ? 'nextWeek' : 'sameElse';


                    };

                    this.time = moment(utcToLocal).calendar(null, {
                        sameHour:  function (now) {
                            return '[' + moment(utcToLocal).fromNow() + ']';
                        },
                        lastDay : '[Yesterday, at] H:mm',
                        sameDay : '[Today, at] H:mm',
                        nextDay : '[Tomorrow, at] H:mm',
                        lastWeek : 'dddd, [at] H:mm',
                        nextWeek : 'dddd, [at] H:mm',
                        sameElse : 'DD.MM.YYYY, [at] H:mm'
                    });
                }
            }
        },

        destroyed () {
            clearInterval(this.intervalId);
        }
    }

</script>