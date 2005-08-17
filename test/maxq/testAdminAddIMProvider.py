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
class testAdminAddIMProvider(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
    
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        self.msg('Test started')

        drupal_path = commonConst.DRUPAL_PATH

        commonAPI.login(self)

        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/IMProvider''') % drupal_path)
        url = "%s/civicrm/admin/IMProvider" % drupal_path
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
        
        params = [
            ('''action''', '''add'''),
            ('''reset''', '''1'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/IMProvider?action=add&reset=1''') % drupal_path)
        url = "%s/civicrm/admin/IMProvider" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        queryName    = 'select name from civicrm_im_provider'          
        queryID      = 'select max(id) from civicrm_im_provider'
        providerName = db.loadRows(queryName)
        providerNum  = db.loadVal(queryID)

        params = [
            ('''_qf_default''', '''IMProvider:next'''),
            ('''name''', '''Test IM'''),
            ('''_qf_IMProvider_next''', '''Save'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/IMProvider?_qf_default=IMProvider:next&name=Test IM&_qf_IMProvider_next=Save''') % drupal_path)
        url = "%s/civicrm/admin/IMProvider" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        for i in range(int(providerNum)) :
            if providerName[i].values()[0] == params[1][1] :
                print ("**************************************************************************************")
                print ("IM Provider \'" + providerName[i].values()[0] + "\' already exists")
                print ("**************************************************************************************")
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 8 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                break
            else :
                continue
        else :
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 9 failed", 302, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''reset''', '''1'''),
            ('''action''', '''browse'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/IMProvider?reset=1&action=browse''') % drupal_path)
        url = "%s/civicrm/admin/IMProvider" % drupal_path
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
    test = testAdminAddIMProvider("testAdminAddIMProvider")
    test.Run()
