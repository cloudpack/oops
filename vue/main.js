/**
 * Created by shinichi on 2016/09/16.
 */
var Vue = require("vue")
Vue.use(require("vue-resource"))

Vue.config.debug = true
Vue.config.devtools = true
Vue.config.delimiters = ["${", "}"]

new Vue({
    el: "#wrapper",
    data: {
        isUploaded: false,
        argType: [
            "array",
            "object"
        ],
        version: ["1.1", "1.2"],

        functions: [],
        soap: {
            name: "",
            function: "",
            header: [],
            argType: "",
            array: [],
            object: []
        },
        result: ""
    },
    methods: {
        upload: function () {
            var that = this
            var file = this.$els.file.files[0]

            var form = new FormData()
            form.append('file', file)

            this.$http.post("/file", form)
                .then(function (response) {
                    that.isUploaded = true
                    that.soap.name = response.body.filename
                    return that.$http.get('/functions', {
                        params: {
                            name: that.soap.name
                        }
                    })
                })
                .then(function (response) {
                    that.functions = response.body.functions
                })
        },

        addHeader: function () {
            this.soap.header.push({
                namespace: "",
                key: "",
                value: ""
            })
        },
        removeHeader: function (index) {
            this.soap.header.splice(index, 1)
        },

        addArray: function () {
            this.soap.array.push('')
        },
        removeArray: function (index) {
            this.soap.array.splice(index, 1)
        },

        addObject: function () {
            this.soap.object.push({key: "", value: ""})
        },
        removeObject: function (index) {
            this.soap.object.splice(index, 1)
        },

        request: function () {
            var that = this
            this.$http.post("/request", that.soap)
                .then(function (response) {
                    that.result = response.body.result
                })
        }
    }
})