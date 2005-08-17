# Generated by MaxQ [com.bitmechanic.maxq.generator.JythonCodeGenerator]
from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import Config
from com.bitmechanic.maxq import DBUtil
import commonConst, commonAPI
global validatorPkg
if __name__ == 'main':
    validatorPkg = Config.getValidatorPkgName()
# Determine the validator for this testcase.
exec 'from '+validatorPkg+' import Validator'


# definition of test class
class testAdminEditCustomData(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
    
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        self.msg('Test started')

        drupal_path = commonConst.DRUPAL_PATH

        commonAPI.login(self)

        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/custom/group''') % drupal_path)
        url = "%s/civicrm/admin/custom/group" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 5 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        #self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        #url = "http://localhost/favicon.ico"
        #params = None
        #Validator.validateRequest(self, self.getMethod(), "get", url, params)
        #self.get(url, params)
        #self.msg("Response code: %s" % self.getResponseCode())
        #self.assertEquals("Assert number 6 failed", 404, self.getResponseCode())
        #Validator.validateResponse(self, self.getMethod(), url, params)
        
        name      = '\'Test Group\''
        query     = 'select id from civicrm_custom_group where name=%s' % name  
        customGID = db.loadVal(query)

        CGID   = '''%s''' % customGID
        params = [
            ('''action''', '''update'''),
            ('''id''', CGID),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/custom/group?action=update&id=3''') % drupal_path)
        url = "%s/civicrm/admin/custom/group" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        #self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        #url = "http://localhost/favicon.ico"
        #params = None
        #Validator.validateRequest(self, self.getMethod(), "get", url, params)
        #self.get(url, params)
        #self.msg("Response code: %s" % self.getResponseCode())
        #self.assertEquals("Assert number 8 failed", 404, self.getResponseCode())
        #Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''_qf_default''', '''Group:next'''),
            ('''title''', '''Test Group'''),
            ('''extends''', '''Individual'''),
            ('''weight''', '''0'''),
            ('''help_pre''', '''This is test group..This is edited now'''),
            ('''is_active''', '''1'''),
            ('''_qf_Group_next''', '''Save'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/custom/group?_qf_default=Group:next&title=Test Group&extends=Individual&weight=0&help_pre=This is test group..This is edited now&is_active=1&_qf_Group_next=Save''') % drupal_path)
        url = "%s/civicrm/admin/custom/group" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 9 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''action''', '''browse'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/custom/group/?action=browse''') % drupal_path)
        url = "%s/civicrm/admin/custom/group/" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 10 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        commonAPI.logout(self)
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testAdminEditCustomData("testAdminEditCustomData")
    test.Run()
