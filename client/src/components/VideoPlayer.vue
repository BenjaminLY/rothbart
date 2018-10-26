<template lang="pug">
div.video(:class="{display: video.control}", @click="displayControls")
    button.button.is-back(@click="$router.push({name: 'LandingPage'})")
        b-icon(icon="arrow-left", pack="fas")
        span Accueil
    div.video-player
        video.main-player
            source(:src="video.src")
        div.buffer(v-if="video.buffering")
            i.fas.fa-spinner.fa-pulse
    div.video-controls(v-if="video.loaded")
        div.video-box
            div.progression
                div.progress-bar#progress
                    progress(min="0", max="100", :value="video.progression")
                    div.indicator(:style="{left: indicator.offsetX + 'px'}")
                        span {{indicator.value}}
                div.info
                    span {{secondsToHms(remaining)}}
            div.control-buttons
                div.start
                    div.control-button
                        button.button-control(v-if="!video.playing", @click="play")
                            i.fas.fa-play
                        button.button-control(v-if="video.playing", @click="pause")
                            i.fas.fa-pause
                    div.control-button.volume
                        div.slider.is-hidden-mobile.is-hidden-tablet-only
                            div.content
                                input(type="range", orient="vertical", v-model="video.volume")
                        button.button-control(v-if="!video.mute", @click="toggleMute")
                            i.fas.fa-volume-up
                        button.button-control(v-if="video.mute", @click="toggleMute")
                            i.fas.fa-volume-off
                div.end
                    div.control-button
                        button.button-control(@click="toggleExpand")
                            i.fas.fa-expand(v-if="!video.fullscreen")
                            i.fas.fa-compress(v-else)
</template>

<script>
import {CONFIGS} from '../configs'
import mixins from '../mixins'
export default {
    mixins: [mixins],
    data () {
        return {
            loading: true,
            indicator: {
                value: 0,
                offsetX: -20
            },
            timeout: null,
            video: {
                loaded: false,
                cover: null,
                buffering: false,
                control: false,
                volume: 70,
                remaining: 0,
                fullscreen: false,
                progressBar: null,
                duration: 0,
                player: null,
                playing: true,
                mute: false,
                progression: 0,
                currentTime: 0,
                src: null
            }
        }
    },
    mounted () {
        if (this.tokens.access_token === null) {
            this.$toast.open({
                duration: 5000,
                message: 'Veuillez vous connecter afin de visionner la vidéo.',
                type: 'is-danger'
            })
            this.$store.dispatch('user/updateUser', null)
            this.$store.dispatch('shareDatas/updateShare', {auth: true})
            this.$router.push({name: 'LandingPage'})
        } else if (this.movie === null || typeof this.movie.src === 'undefined' || this.movie.src === null) {
            this.$router.push({name: 'LandingPage'})
        }
        this.video.title = this.localData.title
        this.video.src = this.movie.src
        this.loading = false
        this.init()
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
        },
        'video.volume' (value) {
            let volumeOn = value > 0
            this.video.mute = !volumeOn
            this.video.player.muted = !volumeOn
            value = Math.round(value)
            this.video.player.volume = value / 100
            this.video.volume = value
        }
    },
    computed: {
        remaining () {
            return this.video.duration - this.video.currentTime
        }
    },
    methods: {
        init () {
            this.video.player = document.querySelector('.main-player')

            this.video.player.volume = this.video.volume / 100
            // video buffering
            this.video.player.onwaiting = () => {
                this.video.buffering = true
            }
            // video end buffering
            this.video.player.onplaying = () => {
                this.video.buffering = false
            }
            // video end buffering
            this.video.player.onended = () => {
                this.video.progression = this.video.currentTime = this.video.player.currentTime = 0
                this.pause()
            }

            // this.video.remaining = this.video.duration = this.video.player.duration

            // launch video
            this.video.playing = true
            this.video.player.play()

            this.video.player.addEventListener('loadedmetadata', () => {
                this.video.duration = this.video.player.duration
                this.video.remaining = this.video.duration
            })

            this.video.player.addEventListener('timeupdate', () => {this.updateProgressBar()}, false)
        },
        displayControls () {
            this.video.control = true
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.video.control = false
            }, 2000);
        },
        play () {
            this.video.duration = this.video.player.duration
            this.video.playing = true
            this.video.player.play()
        },
        pause () {
            this.video.buffering = false
            this.video.playing = false
            this.video.player.pause()
        },
        toggleExpand () {
            let elem
            let rfs
            if (this.video.fullscreen) {
                elem = document
                rfs = elem.exitFullscreen
                    || elem.msExitFullscreen
                    || elem.mozCancelFullScreen
                    || elem.webkitExitFullscreen
                    || elem.cancelFullScreen
                this.video.fullscreen = false
            } else {
                elem = document.body
                rfs = elem.requestFullscreen
                    || elem.webkitRequestFullScreen
                    || elem.mozRequestFullScreen
                    || elem.msRequestFullscreen
                this.video.fullscreen = true
            }
            rfs.call(elem);
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
        updateProgressBar () {
        	this.video.progression = Math.floor((100 / this.video.player.duration) * this.video.player.currentTime)
            this.video.currentTime = Math.floor(this.video.player.currentTime)

            if (this.remaining <= 0) {
                this.video.progression = this.video.currentTime = this.video.player.currentTime = 0
                this.pause()
            }
        },
        percent (event) {
            let progressLength = document.querySelector('.info').offsetLeft
            - document.querySelector('.progress-bar').offsetLeft - 10
            let percent = event.offsetX / progressLength
            return percent
        },
        indicatorMove (event) {
            let percent = this.percent(event)
            this.indicator = {
                value: this.secondsToHms(percent * this.video.player.duration),
                offsetX: event.offsetX - 20
            }
        },
        move (event) {
            let percent = this.percent(event)
            this.video.currentTime = this.video.player.currentTime = percent * this.video.player.duration
            this.video.progression = percent * 100
            this.play()
        },
        secondsToHms (d) {
            d = Number(d)
            let h = Math.floor(d / 3600)
            let m = Math.floor(d % 3600 / 60)
            let s = Math.floor(d % 3600 % 60)

            return (h > 0 ? this.padLeft(h) + ':' : '') +
            (m > 0 ? this.padLeft(m) + ':' : '') +
            (this.padLeft(s))

            return hDisplay + mDisplay + sDisplay
        },
        padLeft (int) {
            if (parseInt(int) < 10) {
                return '0' + int
            } else {
                return int
            }
        }
    }
}
</script>

<style lang="scss">
</style>
