//package org.yarlithub.yschool.integration.utils;
//
//import java.lang.annotation.Annotation;
//import java.lang.reflect.Method;
//import java.util.ArrayList;
//import java.util.List;
//
//import org.junit.runner.Runner;
//import org.junit.runner.notification.RunNotifier;
//import org.junit.runners.BlockJUnit4ClassRunner;
//import org.junit.runners.Parameterized;
//import org.junit.runners.model.FrameworkMethod;
//import org.junit.runners.model.Statement;
//import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
//import org.springframework.util.ReflectionUtils;
//
//public class SpringJUnit4ParameterizedClassRunner extends Parameterized {
//
//	private final List<Runner> runners = new ArrayList<Runner>();
//
//	public SpringJUnit4ParameterizedClassRunner(Class<?> klass) throws Throwable {
//		super(klass);
//
//		for (Runner runner : getSuperChildren()) {
//			runners.add(new SpringParameterizedClassRunner(klass, (BlockJUnit4ClassRunner) runner));
//		}
//	}
//
//	private List<Runner> getSuperChildren() {
//		return super.getChildren();
//	}
//
//	@Override
//	protected List<Runner> getChildren() {
//		return runners;
//	}
//
//	private class SpringParameterizedClassRunner extends SpringJUnit4ClassRunner {
//
//		private BlockJUnit4ClassRunner delegateParameterizedChildRunner;
//
//		public SpringParameterizedClassRunner(Class<?> klass, BlockJUnit4ClassRunner runner) throws Throwable {
//			super(klass);
//			this.delegateParameterizedChildRunner = runner;
//		}
//
//		// simply exists to work-around methods with 'protected' modifier, and as such, are inaccessible from here
//		private Object invokeRunnerMethod(String methodName, Class<?>[] argTypes, Object[] args) {
//			// despite constructor "runner" param, some methods are called via super constructor before this constructor completes
//			if (delegateParameterizedChildRunner == null) {
//				delegateParameterizedChildRunner = (BlockJUnit4ClassRunner) SpringJUnit4ParameterizedClassRunner.this.getSuperChildren().get(runners.size());
//			}
//
//			Method method = ReflectionUtils.findMethod(delegateParameterizedChildRunner.getClass(), methodName, argTypes);
//			ReflectionUtils.makeAccessible(method);
//			return ReflectionUtils.invokeMethod(method, delegateParameterizedChildRunner, args);
//		}
//
//		//@Override
//		protected Statement classBlock(RunNotifier notifier) {
//			return childrenInvoker(notifier);
//		}
//
//		@Override
//		protected Object createTest() throws Exception {
//			Object retVal = invokeRunnerMethod("createTest", null, null);
//			getTestContextManager().prepareTestInstance(retVal);
//			return retVal;
//		}
//
//		@Override
//		protected String getName() {
//			return (String) invokeRunnerMethod("getName", null, null);
//		}
//
//		//@Override
//		protected Annotation[] getRunnerAnnotations() {
//			return (Annotation[]) invokeRunnerMethod("getRunnerAnnotations", null, null);
//		}
//
//		//@Override
//		protected String testName(FrameworkMethod method) {
//			return (String) invokeRunnerMethod("testName", new Class[] { FrameworkMethod.class }, new Object[] { method });
//		}
//
//		//@Override
//		protected void validateConstructor(List<Throwable> errors) {
//			invokeRunnerMethod("validateConstructor", new Class[] { List.class }, new Object[] { errors });
//		}
//
//		//@Override
//		protected void validateFields(List<Throwable> errors) {
//			invokeRunnerMethod("validateFields", new Class[] { List.class }, new Object[] { errors });
//		}
//	}
//}
