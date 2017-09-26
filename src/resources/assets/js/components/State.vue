<template>
    <span :class="'label label-'+color">
        {{ state }}
    </span>
</template>

<script>
    export default {
        props: ['state'],

        computed: {
            color() {
                if (this.state == 'running')
                {
                    return 'info';
                }
                else if (this.state == 'ok')
                {
                    return 'success';
                }
                else if (this.state == 'failed')
                {
                    return 'danger';
                }

                return 'default';
            }
        },
    }
</script>
