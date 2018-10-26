import actions from './actions'
import getters from './getters'
import mutations from './mutations'

var defaultTokens = {
	access_token: null,
	refresh_token: null
}
var storedTokens = window.localStorage.getItem('tokens')
if (storedTokens !== null) {
	defaultTokens = JSON.parse(storedTokens)
}

const defaultState = {
	nextRoute: null,
	tokens: defaultTokens
}

const auth = {
	namespaced: true,
	state: defaultState,
	getters,
	mutations,
	actions
}

export default auth
