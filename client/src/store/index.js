import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

import plans from './modules/plan/index'
import user from './modules/user/index'
import auth from './modules/auth/index'
import shareDatas from './modules/shareDatas/index'
import movie from './modules/movie/index'
import rightAccess from './modules/rightAccess/index'

export default new Vuex.Store({
    modules: {
        plans: plans,
        user: user,
        auth: auth,
        shareDatas: shareDatas,
        movie: movie,
        rightAccess: rightAccess
    }
})
