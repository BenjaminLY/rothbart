import actions from './actions'
import getters from './getters'
import mutations from './mutations'

var defaultData = null
var storedData = window.localStorage.getItem('movie')
if (storedData !== null) {
	defaultData = JSON.parse(storedData)
}
const defaultState = {
	data: defaultData
}

const user = {
	namespaced: true,
	state: defaultState,
	getters,
	mutations,
	actions
}

export default user
