<template lang="pug">
div.disclaimer-page(:style="{'background-image': disclaimerBackground}")
    div.action-buttons
        div
            h1 L'accès à ce site est réservé aux personnes majeures<br/>(à partir de 18 ans en France).<br/>En cliquant sur "Je suis majeur", je certifie avoir l'âge légal requis dans mon pays pour visiter ce site.
        div.columns
            div.column.is-6.has-text-centered
                button.button.is-danger.is-large(@click="notAdult") Je ne suis pas majeur
            div.column.is-6.has-text-centered
                button.button.is-success.is-large(@click="isAdult") Je suis majeur
        div.text
            p En poursuivant votre navigation sur ce site, vous acceptez l’utilisation de cookies pour vous proposer des services et offres adaptés à vos centres d’intérêts.

</template>

<script>
import {EventBus} from '../EventBus'
import {CONFIGS} from '../configs'
import mixins from '../mixins'
export default {
    mixins: [mixins],
    computed: {
        disclaimerBackground () {
            return 'url(' + this.movie.disclaimer + ')'
        }
    },
    methods: {
        isAdult () {
            EventBus.$emit('isAdult')
        },
        notAdult () {
            window.location = 'https://google.com'
        }
    }
}
</script>

<style lang="scss">
.disclaimer-page {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 30;
    background-color: #000;
    background-position: center center;
    background-size: cover;
    h1 {
        font-size: 20px;
        font-weight: 600;
    }
    img {
        width: 100%;
        height: 100%;
        position: absolute;
        z-index: 30;
    }
    .action-buttons {
        position: absolute;
        z-index: 31;
        width: 100%;
        padding: 15px;
        @media only screen and (max-device-width : 767px) {
            left: 0;
            top: 0;
            height: 100vh;
        }
        @media only screen and (min-device-width : 768px) {
            padding: 0;
            right: 15px;
            bottom: 100px;
            width: 50%;
        }
        color: #fff;
        strong {
            color: #fff;
        }
        button.button{
            width: 100%;
        }
    }
    .text {
        font-size: 14px;
        p {
            margin: 10px 0;
        }
    }
}
</style>
