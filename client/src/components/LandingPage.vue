<template lang="pug">
div.home
    div.video-player
        div.filter
        div.cover(:style="{'background-image': video.cover}")
        video.main-player(v-if="video.display")
            source(:src="video.src")
        div.title
            h1 {{movie.title}}
            h2 {{movie.subtitle}}
        div.actions
            div.action-buttons(v-if="!loading")
                div.columns(v-if="!userConnected || (!canDownload && !canStream)")
                    div.column.is-6.has-text-centered
                        button.button.is-buy(@click="buy")
                            div.columns
                                div.column.is-2
                                    span Acheter
                                div.column.is-8.price
                                    span {{buyPrice}} &euro;
                                div.column.is-2
                                    b-icon(icon="download", pack="fas")
                        div Téléchargez le film complet
                    div.column.is-6.has-text-centered
                        button.button.is-rent(@click="rent")
                            div.columns
                                div.column.is-2
                                    span Louer
                                div.column.is-8.price
                                    span {{rentPrice}} &euro;
                                div.column.is-2
                                    b-icon(icon="play", pack="fas")
                        div Visionnez en stream
                div.columns(v-else)
                    div.column.has-text-centered(v-if="canDownload", :class="{'is-6': canStream}")
                        button.button.is-buy(@click="downloadVideo")
                            div.columns
                                div.column.is-10
                                    span Télécharger
                                div.column.is-2
                                    b-icon(icon="download", pack="fas")
                        div Téléchargez le film complet
                    div.column.has-text-centered(v-if="canStream", :class="{'is-6': canDownload}")
                        button.button.is-rent(@click="launchVideo")
                            div.columns
                                div.column.is-10
                                    span Lecture
                                div.column.is-2
                                    b-icon(icon="play", pack="fas")
                        div Visionnez en stream
    footer
        div.columns
            div.column.is-4
                div.columns(v-if="video.display")
                    div.column.is-1
                        button.button.is-transparent(v-if="!video.mute", @click="toggleMute")
                            i.fas.fa-volume-up
                        button.button.is-transparent(v-if="video.mute", @click="toggleMute")
                            i.fas.fa-volume-off
                    div.column.is-11
                        button.button.is-replay.is-transparent(@click="playTrailer", v-if="displayPlayTrailer")
                            i.fas.fa-redo.replay
                            span Rejouer la bande annonce
            div.column.is-4
                div.content.has-text-centered
                    span Ridleyprod 2018
                    a.separator(href="mailto:contact@mafioso-lefilm.com")
                        span Contact
                    span.separator.is-clickable(@click="openPaymentMention") Paiement
                    span.separator.is-clickable(@click="openLegalMention") Mentions légales
            div.column.is-4
                div.content.has-text-right.login.is-bold
                    span.is-clickable(v-if="!userConnected", @click="openSignin") Se connecter
                    span.is-clickable(v-if="userConnected", @click="logout") Se déconnecter
</template>

<script>
import {CONFIGS} from '../configs'
import mixins from '../mixins'
export default {
    mixins: [mixins],
    data () {
        return {
            displayPlayTrailer: false,
            loading: true,
            video: {
                cover: '',
                display: true,
                title: null,
                buffering: false,
                control: false,
                volume: 70,
                remaining: 0,
                fullscreen: false,
                progressBar: null,
                duration: 0,
                player: null,
                playing: true,
                mute: true,
                progression: 0,
                currentTime: 0,
                src: null
            }
        }
    },
    watch: {
        'video.duration' (value) {
            this.video.loaded = true

            setTimeout(() => {
                this.video.progressBar = document.querySelector('.progress-bar')
                this.video.progressBar.addEventListener('click', (event) => {this.move(event)})
                document.querySelector('progress').onmousemove = (event) => {
                    this.indicatorMove(event)
                }

                // display controls
                this.displayControls()
                document.onmousemove = () => {
                    // display controls
                    this.displayControls()
                }

                this.video.player.play()
            }, 2000)
        }
    },
    mounted () {
        this.getRightAccess()
        this.video.src = CONFIGS.api.movie.watch + '?m=' + this.movie.trailer + '&t=trailer'
        this.init()
    },
    methods: {
        init () {
            setTimeout(() => {
                this.video.player = document.querySelector('.main-player')
                this.video.player.play()
                this.video.player.muted = true
                // video end buffering
                this.video.player.onended = () => {
                    this.displayPlayTrailer = true
                }
            }, 2000)
        },
        getRightAccess () {
            this.loading = true
            this.$store.dispatch('rightAccess/update', null)
            this.$http.get(CONFIGS.api.movie.rightAccess.replace(':reference', this.movie.movie)).then(
                (response) => {
                    this.$store.dispatch('rightAccess/update', response.data)
                    this.loading = false
                },
                (error) => {
                    switch (error.response.status) {
                    case 401:
                    this.refreshToken(() => { this.getRightAccess() })
                    break
                    }
                    this.loading = false
                }
            )
        },
        playTrailer () {
            this.video.player = document.querySelector('.main-player')
            this.video.player.play()
            this.displayPlayTrailer = false
        },
        toggleMute () {
            this.video.mute = !this.video.mute
            this.video.player.muted = !this.video.player.muted

            if (!this.video.mute) {
                this.video.volume = 70
                this.video.player.volume = this.video.volume / 100
            } else {
                this.video.volume = 0
            }
        },
        checkConnection () {
            if (this.userConnected) {
                this.launchVideo()
            } else {
                this.auth = true
            }
        },
        buy () {
            this.$store.dispatch('shareDatas/updateShare', 'buy')
            this.$router.push({name: 'SigninPage'})
        },
        rent () {
            this.$store.dispatch('shareDatas/updateShare', 'rent')
            this.$router.push({name: 'SigninPage'})
        }
    }
}
</script>

<style lang="css">
</style>
