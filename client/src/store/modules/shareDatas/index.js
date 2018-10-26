import actions from './actions'
import getters from './getters'
import mutations from './mutations'

let defaultLocalData = null
let storedLocalData = window.localStorage.getItem('tokens')
if (storedLocalData !== null) {
	defaultLocalData = JSON.parse(storedLocalData)
}
const defaultState = {
	data: null,
	share: null,
	localData: defaultLocalData
}

const shareDatas = {
	namespaced: true,
	state: defaultState,
	getters,
	mutations,
	actions
}

export default shareDatas
