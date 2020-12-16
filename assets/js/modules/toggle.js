export default

function toggleStatus (e) {
    e.preventDefault()
    const btn = this
    const url = this.href

    ajax(url, 'PATCH').then(result => {
        btn.innerText = result.value ? 'Publié' : 'Brouillon'
    }).catch(error => {
        console.error(error)
    })
}

const ajax = function (url, method) {
    return new Promise(function (resolve, reject) {
        let request = new XMLHttpRequest()
        request.open(method, url, true)
        request.onreadystatechange = function() {
            if (request.readyState === 4) {
                if (request.status === 200){
                    resolve(JSON.parse(request.response))
                } else {
                    reject(request)
                }
            }
        }
        request.send()
    })
}