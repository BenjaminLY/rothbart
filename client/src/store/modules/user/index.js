import actions from './actions'
import getters from './getters'
import mutations from './mutations'

var defaultUser = null
var storedUser = window.localStorage.getItem('user')
if (storedUser !== null) {
	defaultUser = JSON.parse(storedUser)
}
const defaultState = {
	user: defaultUser
}

const user = {
	namespaced: true,
	state: defaultState,
	getters,
	mutations,
	actions
}

export default user
