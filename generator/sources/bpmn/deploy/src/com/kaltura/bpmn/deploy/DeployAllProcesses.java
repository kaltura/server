package com.kaltura.bpmn.deploy;

import java.io.File;
import java.io.FileInputStream;
import java.util.zip.ZipInputStream;

import org.activiti.engine.ProcessEngine;
import org.activiti.engine.ProcessEngineConfiguration;
import org.activiti.engine.RepositoryService;

public class DeployAllProcesses {

	public static void main(String[] args) throws Exception {
		if(args.length < 1){
			throw new Exception("Business archive path is required argument");
		}
		
		String barFileName = args[0];
		File file = new File(barFileName);
		if(!file.exists()){
			throw new Exception("Business archive [" + barFileName + "] not found");
		}
		System.out.println("Deploying [" + barFileName + "]");
		
		ProcessEngine processEngine = ProcessEngineConfiguration.createProcessEngineConfigurationFromResourceDefault().buildProcessEngine();
		RepositoryService repositoryService = processEngine.getRepositoryService();
    	ZipInputStream inputStream = new ZipInputStream(new FileInputStream(file));
    	repositoryService.createDeployment().name(file.getName()).addZipInputStream(inputStream).deploy();
	}
}