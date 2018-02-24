/**
 * Created by KF on 2018-02-10.
 */
js_comm = {
    alert: function(s){
        alert(s);
    },
    ajax_running: 0,
    ajax: function(url, data, ok_call, err_call) {
        var self = this;
        self.ajax_running++;
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'JSON',
            success: function(res){
                self.handle_ajax_json_res(res, ok_call, err_call);
            },
            error: function(err){
                self.alert('服务器开小差了..请稍后再试');
            },
            complete: function(){
                self.ajax_running--;
            }
        })
    },
    ajax_get: function(url, ok_call) {
        var self = this;
        self.ajax_running++;
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'TEXT',
            success: function(res){
                ok_call(res);
            },
            error: function(err){
                self.alert('服务器开小差了..请稍后再试');
            },
            complete: function(){
                self.ajax_running--;
            }
        })
    },
    ajax_form: function(frm, append_data, ok_call, err_call) {
        var self = this;
        var $frm = $(frm),
            url = $frm.attr('action'),
            data = $frm.serialize();
        if (append_data) {
            data += '&'+append_data;
        }
        self.ajax(url, data, ok_call, err_call);
    },
    handle_ajax_json_res: function(res, ok_call, err_call) {
        if (res.status === 0) {
            if (ok_call) {
                ok_call(res);
            }
        } else {
            if (err_call) {
                err_call(res)
            } else {
                switch (res.code) {
                    case 0:
                    default:
                        alert('错误代码：'+res.code+'; 错误信息：'+res.content);
                }
            }
        }
    },
    ini: function() {
        console.log('It works.');
    }
};
$(function(){
    js_comm.ini();
});
