const actions = {
	updateSessionStorageDatas (context, value) {
		context.commit('UPDATE_SESSION_STORAGE_DATA', value)
	},
	updateDatas (context, value) {
		context.commit('UPDATE_DATA', value)
	},
	updateShare (context, value) {
		context.commit('UPDATE_SHARE', value)
	}
}

export default actions
