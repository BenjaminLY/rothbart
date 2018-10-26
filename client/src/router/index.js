import Vue from 'vue'
import Router from 'vue-router'
import LandingPage from '@/components/LandingPage'
import VideoPlayer from '@/components/VideoPlayer'
import SigninPage from '@/components/Signin'
import Signin from '@/components/Auth/Signin'
import Signup from '@/components/Auth/Signup'
import Confirmation from '@/components/Auth/Confirmation'
import SuccessPage from '@/components/Payment/Success'
import FailedPage from '@/components/Payment/Failed'

Vue.use(Router)

export default new Router({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'LandingPage',
            component: LandingPage
        },
        {
            path: '/watch',
            name: 'VideoPlayer',
            component: VideoPlayer
        },
        {
            path: '/auth/confirmation/:token',
            name: 'Confirmation',
            component: Confirmation
        },
        {
            path: '/signin',
            name: 'SigninPage',
            component: SigninPage
        },
        {
            path: '/signup',
            name: 'Signup',
            component: Signup
        },
        {
            path: '/payment/success',
            name: 'SuccessPage',
            component: SuccessPage
        },
        {
            path: '/payment/failed',
            name: 'FailedPage',
            component: FailedPage
        }
    ]
})
