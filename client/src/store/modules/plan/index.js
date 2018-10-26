import actions from './actions'
import getters from './getters'
import mutations from './mutations'

const defaultState = {
	plans: []
}

const shareDatas = {
	namespaced: true,
	state: defaultState,
	getters,
	mutations,
	actions
}

export default shareDatas
