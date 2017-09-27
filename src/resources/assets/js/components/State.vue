<template>
    <span :class="'label label-'+color">
        {{ text ? text : state }}
    </span>
</template>

<script>
    export default {
        props: ['state', 'text'],

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
                else if (this.state == 'disabled')
                {
                    return 'warning';
                }
                else if (this.state == 'enabled')
                {
                    return 'primary';
                }

                return 'default';
            }
        },
    }
</script>
