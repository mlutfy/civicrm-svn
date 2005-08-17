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
class testEditNoteByContactTab(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
    
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        self.msg('Test started')

        drupal_path = commonConst.DRUPAL_PATH

        commonAPI.login(self)

        name    = 'Zope, Manish'
        queryID = 'select id from civicrm_contact where sort_name=\'%s\'' % name

        contactID = db.loadVal(queryID)
        if contactID :
            CID = '''%s''' % contactID
            
            note      = 'This is Test Note From Contact Tab'
            queryID   = 'select id from civicrm_note where note like \'%%%s%%\'' % note
            noteID    = db.loadVal(queryID)
            
            if noteID :
                NID = '''%s''' % noteID
                
                params = [
                    ('''reset''', '''1'''),
                    ('''cid''', CID),]
                url = "http://localhost/drupal/civicrm/contact/view"
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 1 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                
                        
                params = [
                    ('''nid''', NID),
                    ('''action''', '''update'''),]
                url = "%s/civicrm/contact/view/note" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
        
                params = [
                    ('''_qf_default''', '''Note:next'''),
                    ('''note''', '''This is Test Note from Contact Tab...Doing test for Editing the Note'''),
                    ('''_qf_Note_next''', '''Save'''),]
                url = "%s/civicrm/contact/view/note" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "post", url, params)
                self.post(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 9 failed", 302, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)

                editNote = params[1][1]
                params = [
                    ('''action''', '''browse'''),]
                url = "%s/civicrm/contact/view/note" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 10 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)

                if self.responseContains('%s' % editNote) :
                    print ("**************************************************************************************")
                    print "The Note \'%s\' is Edited Successfully" % note
                    print ("**************************************************************************************")
                else :
                    print ("**************************************************************************************")
                    print "Some Problem while Editing \'%s\' Note" % note
                    print ("**************************************************************************************")
            else :
                print ("**************************************************************************************")
                print ("There is no Note like \'%s\'") % note
                print ("**************************************************************************************")
        else :
            print "********************************************************************************"
            print "Required Contact Does not Exists"
            print "********************************************************************************"

        commonAPI.logout(self)
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testEditNoteByContactTab("testEditNoteByContactTab")
    test.Run()
