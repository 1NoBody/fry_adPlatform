<template>
    <el-dialog
        title="账号注册"
        :visible.sync=centerDialogVisible 
        :show-close=false
        width="30%"
        :center=true
        :close-on-click-modal=false
        >
        <el-form :model="ruleForm" status-icon :rules="rules" ref="ruleForm" label-width="70px" class="demo-ruleForm">
           <el-form-item label="账号" prop="account">
                <el-input type="text" v-model="ruleForm.account" autocomplete="off"></el-input>
            </el-form-item>
            <el-form-item label="密码" prop="pass">
                <el-input type="password" v-model="ruleForm.pass" autocomplete="off"></el-input>
            </el-form-item>
            <el-form-item label="确认密码" prop="checkPass">
                <el-input type="password" v-model="ruleForm.checkPass" autocomplete="off"></el-input>
            </el-form-item>
            <el-form-item label="验证码" prop="captcha">           
              <el-input id='verify' v-model="ruleForm.verify" @blur="verify(ruleForm.verify)" :disabled="isDisabled"></el-input>
              <span @click="flush">
                <img id='captcha' width='170px' :src="imgUrl" /> 
              </span>
            </el-form-item>
            <el-form-item id='button'>
                <el-button type="primary" @click="submitForm('ruleForm')">提交</el-button>
                <el-button @click="back">取消</el-button>
            </el-form-item>
        </el-form>
    </el-dialog>
</template>

<script>
import { post, get } from "@/api/api.js";
  export default {
    data() {
      var validateCaptcha = (rule, value, callback) => {
        if (!value) {
          return callback(new Error('验证码不能为空'));
        }
        callback();
      };
      var validateAccount = (rule, value, callback) => {
        if (value === '') {
          return callback(new Error('请输入账号'));
        }
        callback();
      };
      var validatePass = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入密码'));
        } 
        if (value.length < 6) {    
          callback(new Error('密码长度不能小于6位数'))
        }
        else {
          if (this.ruleForm.checkPass !== '') {
            this.$refs.ruleForm.validateField('checkPass');
          }
          callback();
        }
      };
      var validatePass2 = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请再次输入密码'));
        } else if (value !== this.ruleForm.pass) {
          callback(new Error('两次输入密码不一致!'));
        } else {
          callback();
        }
      };
      return {
        centerDialogVisible: true,
        imgUrl:'http://192.168.168.21:13000/user/verify?time=' + Date.now(),
        isDisabled: false, // 验证成功后禁用输入框
        ruleForm: {
          account:'',
          pass: '',
          checkPass: '',
          verify: ''
        },
        rules: {
          account: [
            { validator: validateAccount, trigger: 'blur' }
          ],
          pass: [
            { validator: validatePass, trigger: 'blur' }
          ],
          checkPass: [
            { validator: validatePass2, trigger: 'blur' }
          ],
          verify: [
            { validator: validateCaptcha, trigger: 'blur' }
          ]
        }
      };
    },
    methods: {
      submitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
              if(this.isDisabled){  // 验证码校验通过
                var data = {'username': this.ruleForm.account, 'password': this.ruleForm.checkPass};
                  post("user/register", data).then((res) => {
                    this.$router.push({path: '/login'});
                  })
                  .catch((e) => {});
              }         
          } else {
            console.log('error submit!!');
            return false;
          }
        });
      },
      back() {
        this.$router.push({ path: '/' }) 
      },
      verify(captcha) {
          get("user/verify", { captcha }).then((res) => {
            this.isDisabled = true;
          })
        .catch((e) => {});
      },
      flush() {
        this.imgUrl = 'http://192.168.168.21:13000/user/verify?time=' + Date.now();
        this.isDisabled = false;
      }
    },
  }
</script>

<!-- 覆盖element ui属性 -->
<style>
  #verify{
    width: 160px;
  }
  #captcha{
    float: right;
    margin-top: -45px;
  }
  .el-form {
      margin-right: 50px;
  }
  #button{
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-around;
  }
</style>


<style lang="scss">
/* 修复input 背景不协调 和光标变色 */
/* Detail see https://github.com/PanJiaChen/vue-element-admin/pull/927 */
$bg:#283443;
$light_gray:#fff;
$cursor: #fff;
</style>