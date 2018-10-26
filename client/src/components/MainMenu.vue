<template lang="pug">
div.main-menu
    nav.navbar(role="navigation", aria-label="main navigation")
        div.container
            router-link.navbar-item.is-bold(:to="{name: 'LandingPage'}") {{movie.title}}
</template>

<script>
import {CONFIGS} from '../configs'
import mixins from '../mixins'
export default {
    mixins: [mixins]
}
</script>

<style lang="css">
</style>
