<template>
  <div class="infor">
    <div class="table">
      <el-table align="center" :data="tableData" border style="width: 750px">
        <el-table-column fixed prop="company" label="企业名称" width="150">
        </el-table-column>
        <el-table-column prop="name" label="联系人名字" width="160">
        </el-table-column>
        <el-table-column prop="telephone" label="联系电话" width="158">
        </el-table-column>
        <el-table-column prop="audit" label="审核状态" width="160">
          <template slot-scope="scope">
            <span v-if="scope.row.audit==1">{{"审核中"}}</span>
            <span v-if="scope.row.audit==2">{{"未通过"}}</span>
            <span v-if="scope.row.audit==3">{{"已审核"}}</span>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="操作" width="120">
          <template slot-scope="scope">
            <el-button @click="handleClick(scope.row.user_id)" type="text" size="small"
              >查看</el-button>
            <el-button
              type="text"
              @click="centerDialogVisible = true,operate_id=scope.row.id"
              size="small"
              >编辑</el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <div class="popup">
      <el-dialog
        title="企业审核"
        :visible.sync="centerDialogVisible"
        width="30%"
        :destroy-on-close=true
        center
      >
        <el-form label-width="70px" class="demo-ruleForm" >
          <div class="radio">
            <el-radio v-model="radio" label="3">审核通过</el-radio>
            <el-radio v-model="radio" label="2">审核失败</el-radio>
          </div>
          <el-form-item label="附加信息">
            <el-input type="textarea" v-model="text"></el-input>
          </el-form-item>
          <div class="btn">
            <el-form-item>
              <el-button @click="resetForm">取 消</el-button>
              <el-button type="primary" @click="submit"
                >提 交</el-button>
            </el-form-item>
          </div>
        </el-form>
      </el-dialog>
    </div>
    <div class="block">
      <el-pagination
        align="center"
        :page-size=pageInfo.num
        :pager-count="11"
        :total=pageInfo.total
        layout="prev, pager, next"
        :current-page.sync=pageInfo.page
        @current-change="changePage"
      >
      </el-pagination>
    </div>
  </div>
</template>

<script>
import { post,get } from "@/api/api.js";

export default {
  methods: {
    handleClick(id) {
      //console.log(id);
      this.$router.push({ name:'user/auth', params: {id: id}}) 
    },
    resetForm() {
      this.centerDialogVisible = false
    },
    changePage() {
        get("company/findAll", this.pageInfo).then(res => {
            this.tableData = res['lists']['data'];
        }).catch(err => {});
    },
    submit() {
      var auditInfo = {id: this.operate_id, audit:this.radio, reason:this.text};
      post('company/audit', auditInfo).then(res => {
        this.centerDialogVisible = false;
        this.changePage();
      }).catch(err => {});
    },
  },
  data() {
    return {
      radio: "3",
      text:"",
      operate_id:0,
      centerDialogVisible: false,
      pageInfo:{
        num: 10,
        page: 1,
        total: 0
      },
      tableData: []
    };
  },
  created() {
      get('company/findAll', this.pageInfo).then(res => {
        this.tableData = res['lists']['data'];
        this.pageInfo.total = res['lists']['total'];
      }).catch(err => {});
  }
};
</script>

<style scoped>
.infor {
  height: 800px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.block {
  margin-top: 50px;
}
.demo-ruleForm {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.radio{
    margin-bottom: 20px;
}
.btn{
    margin-right: 80px;
}
</style>