<template>

    <span class="status" v-bind:class="{offline: isOffline}">{{ timeFromNow }}</span>

</template>

<script>

    import moment from 'moment'

    export default {

        props: {
            created: {
                type: String,
                required: true,
            }
        },

        data () {
            return {
                timeFromNow: null,
                intervalId: null,
            }
        },

        computed: {
            isOffline: function () {
                return (this.timeFromNow === 'Offline');
            }
        },

        created () {

            this.getTimeFromNow();
            this.intervalId = setInterval(this.getTimeFromNow, 5000);

        },

        methods: {
            getTimeFromNow () {
                if (moment(this.created, "YYYY-MM-DD HH:mm:ss").isValid())
                {
                    this.timeFromNow = 'Online for ' + moment(moment.utc(this.created, "YYYY-MM-DD HH:mm:ss").local()).fromNow(true);

                }

                else
                {
                    this.timeFromNow = 'Offline';
                }
            }
        },

        destroyed () {
            clearInterval(this.intervalId);
        }
    }

</script>